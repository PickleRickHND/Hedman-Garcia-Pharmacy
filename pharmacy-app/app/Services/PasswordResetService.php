<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PasswordResetCode;
use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Recuperación de contraseña por código (OTP) de un solo uso.
 *
 * - El código se guarda hasheado; nunca se persiste en claro.
 * - `sendCode` es silencioso: no revela si el correo existe (anti-enumeración),
 *   y limita los envíos por correo (no solo por IP) para evitar mail-bombing y
 *   el DoS de invalidar el código vigente de la víctima.
 * - El reset es atómico (transacción + lock), acota los intentos por código
 *   (anti fuerza-bruta), invalida los códigos restantes y revoca los tokens.
 */
class PasswordResetService
{
    /** Intentos de verificación fallidos antes de invalidar el código. */
    private const MAX_ATTEMPTS = 5;

    /** Envíos permitidos por correo dentro de la ventana del TTL. */
    private const MAX_SENDS_PER_WINDOW = 3;

    public function ttlMinutes(): int
    {
        return (int) config('pharmacy.password_reset.code_ttl_minutes', 15);
    }

    public function codeLength(): int
    {
        return (int) config('pharmacy.password_reset.code_length', 6);
    }

    /**
     * Genera y envía un código al usuario si el correo está registrado.
     * No lanza ni revela nada cuando el correo no existe.
     */
    public function sendCode(string $email): void
    {
        // Límite por correo (no solo por IP): se evalúa antes de comprobar
        // existencia para que el comportamiento sea uniforme y no enumere cuentas.
        $key = 'pwreset-send:'.sha1(Str::lower($email));
        if (RateLimiter::tooManyAttempts($key, self::MAX_SENDS_PER_WINDOW)) {
            return;
        }
        RateLimiter::hit($key, $this->ttlMinutes() * 60);

        $user = User::where('email', $email)->first();

        if (! $user) {
            return;
        }

        // Un único código vigente por usuario: descarta los anteriores no usados.
        $this->clearPendingCodes($user->id);

        $code = $this->generateCode();

        PasswordResetCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($this->ttlMinutes()),
        ]);

        $user->notify(new PasswordResetCodeNotification($code, $this->ttlMinutes()));
    }

    /**
     * Restablece la contraseña si el código es válido (vigente, no usado, coincide).
     * Devuelve false ante cualquier fallo, sin distinguir la causa.
     *
     * La verificación y el consumo del código ocurren dentro de una transacción
     * con bloqueo de fila, de modo que dos peticiones concurrentes con el mismo
     * código no puedan completar el reset dos veces.
     */
    public function reset(string $email, string $code, string $password): bool
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return false;
        }

        return DB::transaction(function () use ($user, $code, $password): bool {
            $record = PasswordResetCode::where('user_id', $user->id)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $record) {
                return false;
            }

            if (! Hash::check($code, $record->code_hash)) {
                $record->increment('attempts');

                // Demasiados intentos: quema el código para forzar re-solicitud.
                if ($record->attempts >= self::MAX_ATTEMPTS) {
                    $record->forceFill(['used_at' => now()])->save();
                }

                return false;
            }

            // El cast 'hashed' del modelo User hashea la contraseña al guardarla.
            $user->forceFill([
                'password' => $password,
                'must_change_password' => false,
            ])->save();

            $record->forceFill(['used_at' => now()])->save();

            // Cierra el flujo: sin códigos pendientes y sin sesiones previas activas.
            $this->clearPendingCodes($user->id);
            $user->tokens()->delete();

            return true;
        });
    }

    /** Descarta los códigos vigentes (no usados) del usuario. */
    private function clearPendingCodes(int $userId): void
    {
        PasswordResetCode::where('user_id', $userId)->whereNull('used_at')->delete();
    }

    /** Código numérico de longitud fija (con ceros a la izquierda), sin sesgo. */
    private function generateCode(): string
    {
        $length = max(4, $this->codeLength());
        $max = (10 ** $length) - 1;

        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }
}
