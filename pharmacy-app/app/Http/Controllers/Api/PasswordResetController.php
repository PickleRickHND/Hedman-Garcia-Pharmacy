<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Recuperación de contraseña por código (OTP) para el cliente Angular.
 * Rutas públicas y con throttle (ver routes/api.php).
 */
class PasswordResetController extends Controller
{
    /**
     * Solicita un código de recuperación. Respuesta genérica para no revelar
     * si el correo existe (anti-enumeración de cuentas).
     */
    public function forgot(ForgotPasswordRequest $request, PasswordResetService $service): JsonResponse
    {
        $service->sendCode((string) $request->string('email'));

        return response()->json([
            'message' => 'Si el correo está registrado, te enviamos un código de recuperación.',
        ]);
    }

    /**
     * Restablece la contraseña con el código recibido.
     *
     * @throws ValidationException
     */
    public function reset(ResetPasswordRequest $request, PasswordResetService $service): JsonResponse
    {
        $ok = $service->reset(
            (string) $request->string('email'),
            (string) $request->string('code'),
            (string) $request->string('password'),
        );

        if (! $ok) {
            throw ValidationException::withMessages([
                'code' => 'El código es inválido o expiró. Solicita uno nuevo.',
            ]);
        }

        return response()->json([
            'message' => 'Tu contraseña fue actualizada. Ya puedes iniciar sesión.',
        ]);
    }
}
