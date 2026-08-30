<?php

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

it('preserva la serializacion de sesiones durante la migracion', function () {
    expect(config('session.serialization'))->toBe('php');
});

it('usa el middleware vigente para proteger solicitudes stateful', function () {
    expect(config('sanctum.middleware.validate_csrf_token'))
        ->toBe(PreventRequestForgery::class);
});

it('bloquea la deserializacion de clases arbitrarias en cache', function () {
    expect(config('cache.serializable_classes'))->toBeFalse();
});
