<?php

use App\Models\User;

// ------------------------------------------------------------------
// Autorización
// ------------------------------------------------------------------

it('rechaza acceso sin token (401)', function () {
    $this->getJson('/api/users')->assertUnauthorized();
});

it('rechaza a un Cajero (403)', function () {
    apiAs('Cajero');

    $this->getJson('/api/users')->assertForbidden();
});

it('permite a un Administrador listar usuarios', function () {
    apiAs('Administrador');

    $this->getJson('/api/users')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'email', 'role', 'roles', 'must_change_password']], 'meta']);
});

// ------------------------------------------------------------------
// Index: filtros
// ------------------------------------------------------------------

it('filtra usuarios por búsqueda de nombre o correo', function () {
    apiAs('Administrador');
    createUserWithRole('Cajero', ['name' => 'Zoraida Mejía', 'email' => 'zoraida@pharmacy.hn']);

    $this->getJson('/api/users?search=zoraida')
        ->assertOk()
        ->assertJsonFragment(['email' => 'zoraida@pharmacy.hn']);
});

it('filtra usuarios por rol', function () {
    apiAs('Administrador');
    createUserWithRole('Cajero', ['name' => 'Cajero Uno']);

    $res = $this->getJson('/api/users?role=Cajero')->assertOk()->json('data');

    expect(collect($res)->pluck('role')->unique()->all())->toBe(['Cajero']);
});

// ------------------------------------------------------------------
// Store
// ------------------------------------------------------------------

it('crea un usuario y le asigna el rol', function () {
    apiAs('Administrador');

    $this->postJson('/api/users', [
        'name' => 'Nuevo Cajero',
        'email' => 'nuevo@pharmacy.hn',
        'role' => 'Cajero',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'must_change_password' => true,
    ])->assertCreated()->assertJsonPath('data.role', 'Cajero');

    $user = User::where('email', 'nuevo@pharmacy.hn')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasRole('Cajero'))->toBeTrue()
        ->and($user->must_change_password)->toBeTrue();
});

it('valida campos requeridos al crear (422)', function () {
    apiAs('Administrador');

    $this->postJson('/api/users', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'role', 'password']);
});

it('rechaza email duplicado al crear (422)', function () {
    apiAs('Administrador');
    createUserWithRole('Cajero', ['email' => 'repetido@pharmacy.hn']);

    $this->postJson('/api/users', [
        'name' => 'Otro',
        'email' => 'repetido@pharmacy.hn',
        'role' => 'Cajero',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ])->assertStatus(422)->assertJsonValidationErrors(['email']);
});

it('rechaza contraseña sin confirmación coincidente (422)', function () {
    apiAs('Administrador');

    $this->postJson('/api/users', [
        'name' => 'Otro',
        'email' => 'otro@pharmacy.hn',
        'role' => 'Cajero',
        'password' => 'Password123!',
        'password_confirmation' => 'distinta',
    ])->assertStatus(422)->assertJsonValidationErrors(['password']);
});

// ------------------------------------------------------------------
// Update — incluye regresión del fix must_change_password
// ------------------------------------------------------------------

it('actualiza nombre, correo y rol', function () {
    apiAs('Administrador');
    $user = createUserWithRole('Cajero');

    $this->putJson("/api/users/{$user->id}", [
        'name' => 'Editado',
        'email' => 'editado@pharmacy.hn',
        'role' => 'Administrador',
    ])->assertOk()->assertJsonPath('data.role', 'Administrador');

    expect($user->fresh()->name)->toBe('Editado')
        ->and($user->fresh()->hasRole('Administrador'))->toBeTrue();
});

it('respeta must_change_password=true en edición sin cambiar la contraseña (regresión)', function () {
    apiAs('Administrador');
    $user = createUserWithRole('Cajero', ['must_change_password' => false]);
    $originalHash = $user->password;

    $this->putJson("/api/users/{$user->id}", [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'Cajero',
        'must_change_password' => true,
    ])->assertOk();

    expect($user->fresh()->must_change_password)->toBeTrue()
        ->and($user->fresh()->password)->toBe($originalHash); // contraseña intacta
});

it('no cambia la contraseña en edición si no se envía', function () {
    apiAs('Administrador');
    $user = createUserWithRole('Cajero');
    $originalHash = $user->password;

    $this->putJson("/api/users/{$user->id}", [
        'name' => 'Sin pass',
        'email' => $user->email,
        'role' => 'Cajero',
    ])->assertOk();

    expect($user->fresh()->password)->toBe($originalHash);
});

// ------------------------------------------------------------------
// Destroy
// ------------------------------------------------------------------

it('elimina un usuario', function () {
    apiAs('Administrador');
    $target = createUserWithRole('Cajero');

    $this->deleteJson("/api/users/{$target->id}")->assertNoContent();

    expect(User::find($target->id))->toBeNull();
});

it('impide que un administrador se elimine a sí mismo (422)', function () {
    $admin = apiAs('Administrador');

    $this->deleteJson("/api/users/{$admin->id}")
        ->assertStatus(422)
        ->assertJsonPath('message', 'No puedes eliminar tu propia cuenta.');

    expect(User::find($admin->id))->not->toBeNull();
});

// ------------------------------------------------------------------
// Roles
// ------------------------------------------------------------------

it('lista los roles disponibles', function () {
    apiAs('Administrador');

    $this->getJson('/api/roles')
        ->assertOk()
        ->assertJsonFragment(['data' => ['Administrador', 'Cajero', 'Invitado']]);
});
