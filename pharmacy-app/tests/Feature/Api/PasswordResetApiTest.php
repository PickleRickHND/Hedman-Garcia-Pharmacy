<?php

use App\Models\PasswordResetCode;
use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

it('envía un código y responde de forma genérica para un correo registrado', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'ana@pharmacy.hn']);

    $this->postJson('/api/forgot-password', ['email' => 'ana@pharmacy.hn'])
        ->assertOk()
        ->assertJsonStructure(['message']);

    expect(PasswordResetCode::where('user_id', $user->id)->count())->toBe(1);
    Notification::assertSentTo($user, PasswordResetCodeNotification::class);
});

it('no revela si el correo no existe (misma respuesta, sin código ni envío)', function () {
    Notification::fake();

    $this->postJson('/api/forgot-password', ['email' => 'nadie@pharmacy.hn'])
        ->assertOk()
        ->assertJsonStructure(['message']);

    expect(PasswordResetCode::count())->toBe(0);
    Notification::assertNothingSent();
});

it('valida que el correo sea obligatorio y con formato', function () {
    $this->postJson('/api/forgot-password', ['email' => 'no-es-correo'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

it('reemplaza el código anterior por uno nuevo al solicitarlo otra vez', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'ana@pharmacy.hn']);

    $this->postJson('/api/forgot-password', ['email' => 'ana@pharmacy.hn'])->assertOk();
    $this->postJson('/api/forgot-password', ['email' => 'ana@pharmacy.hn'])->assertOk();

    // Solo queda un código vigente (los previos no usados se descartan).
    expect(PasswordResetCode::where('user_id', $user->id)->whereNull('used_at')->count())->toBe(1);
});

it('restablece la contraseña con un código válido y revoca los tokens', function () {
    $user = User::factory()->create([
        'email' => 'ana@pharmacy.hn',
        'must_change_password' => true,
    ]);
    $user->createToken('previo');
    $record = PasswordResetCode::create([
        'user_id' => $user->id,
        'code_hash' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => 'ana@pharmacy.hn',
        'code' => '123456',
        'password' => 'nuevaClave123',
        'password_confirmation' => 'nuevaClave123',
    ])->assertOk()->assertJsonStructure(['message']);

    $user->refresh();
    expect(Hash::check('nuevaClave123', $user->password))->toBeTrue()
        ->and($user->must_change_password)->toBeFalse()
        ->and($user->tokens()->count())->toBe(0)
        ->and($record->fresh()->used_at)->not->toBeNull();
});

it('rechaza un código incorrecto', function () {
    $user = User::factory()->create(['email' => 'ana@pharmacy.hn']);
    PasswordResetCode::create([
        'user_id' => $user->id,
        'code_hash' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => 'ana@pharmacy.hn',
        'code' => '999999',
        'password' => 'nuevaClave123',
        'password_confirmation' => 'nuevaClave123',
    ])->assertStatus(422)->assertJsonValidationErrors('code');
});

it('rechaza un código expirado', function () {
    $user = User::factory()->create(['email' => 'ana@pharmacy.hn']);
    PasswordResetCode::create([
        'user_id' => $user->id,
        'code_hash' => Hash::make('123456'),
        'expires_at' => now()->subMinute(),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => 'ana@pharmacy.hn',
        'code' => '123456',
        'password' => 'nuevaClave123',
        'password_confirmation' => 'nuevaClave123',
    ])->assertStatus(422)->assertJsonValidationErrors('code');
});

it('rechaza un código ya usado', function () {
    $user = User::factory()->create(['email' => 'ana@pharmacy.hn']);
    PasswordResetCode::create([
        'user_id' => $user->id,
        'code_hash' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
        'used_at' => now(),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => 'ana@pharmacy.hn',
        'code' => '123456',
        'password' => 'nuevaClave123',
        'password_confirmation' => 'nuevaClave123',
    ])->assertStatus(422)->assertJsonValidationErrors('code');
});

it('invalida el código tras 5 intentos fallidos (anti fuerza bruta)', function () {
    $user = User::factory()->create(['email' => 'ana@pharmacy.hn']);
    PasswordResetCode::create([
        'user_id' => $user->id,
        'code_hash' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(15),
    ]);

    // 5 intentos con código equivocado.
    foreach (range(1, 5) as $i) {
        $this->postJson('/api/reset-password', [
            'email' => 'ana@pharmacy.hn',
            'code' => '000000',
            'password' => 'nuevaClave123',
            'password_confirmation' => 'nuevaClave123',
        ])->assertStatus(422);
    }

    // El código quedó quemado: ni siquiera el código correcto funciona ya.
    $this->postJson('/api/reset-password', [
        'email' => 'ana@pharmacy.hn',
        'code' => '123456',
        'password' => 'nuevaClave123',
        'password_confirmation' => 'nuevaClave123',
    ])->assertStatus(422);

    expect(PasswordResetCode::where('user_id', $user->id)->whereNull('used_at')->count())->toBe(0);
});

it('al re-solicitar, el código anterior deja de ser válido', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'ana@pharmacy.hn']);

    // Código viejo conocido, luego el usuario pide uno nuevo (que reemplaza al anterior).
    PasswordResetCode::create([
        'user_id' => $user->id,
        'code_hash' => Hash::make('111111'),
        'expires_at' => now()->addMinutes(15),
    ]);
    $this->postJson('/api/forgot-password', ['email' => 'ana@pharmacy.hn'])->assertOk();

    // El código viejo ya no sirve.
    $this->postJson('/api/reset-password', [
        'email' => 'ana@pharmacy.hn',
        'code' => '111111',
        'password' => 'nuevaClave123',
        'password_confirmation' => 'nuevaClave123',
    ])->assertStatus(422)->assertJsonValidationErrors('code');
});

it('limita los envíos por correo dentro de la ventana (anti mail-bombing)', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'ana@pharmacy.hn']);

    // 4 solicitudes seguidas: el endpoint siempre responde 200 (genérico),
    // pero solo se envían hasta 3 notificaciones por correo en la ventana.
    foreach (range(1, 4) as $i) {
        $this->postJson('/api/forgot-password', ['email' => 'ana@pharmacy.hn'])->assertOk();
    }

    Notification::assertSentToTimes($user, PasswordResetCodeNotification::class, 3);
});

it('exige confirmación y longitud mínima de la nueva contraseña', function () {
    User::factory()->create(['email' => 'ana@pharmacy.hn']);

    $this->postJson('/api/reset-password', [
        'email' => 'ana@pharmacy.hn',
        'code' => '123456',
        'password' => 'corta',
        'password_confirmation' => 'otra',
    ])->assertStatus(422)->assertJsonValidationErrors('password');
});
