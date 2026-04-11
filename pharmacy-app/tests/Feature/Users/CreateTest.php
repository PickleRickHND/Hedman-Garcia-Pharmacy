<?php

use App\Livewire\Users\Create;
use App\Models\User;
use Livewire\Livewire;

test('guests cannot access create form', function () {
    $this->get(route('users.create'))->assertRedirect('/login');
});

test('cashier cannot access create form', function () {
    loginAs('Cajero');

    $this->get(route('users.create'))->assertForbidden();
});

test('admin can access create form', function () {
    loginAs('Administrador');

    $this->get(route('users.create'))->assertOk();
});

test('admin can create a new cashier with valid data', function () {
    loginAs('Administrador');

    Livewire::test(Create::class)
        ->set('name', 'Nuevo Cajero')
        ->set('email', 'nuevo@pharmacy.hn')
        ->set('password', 'secret-password-123')
        ->set('password_confirmation', 'secret-password-123')
        ->set('role', 'Cajero')
        ->set('must_change_password', true)
        ->call('save')
        ->assertRedirect(route('users.index'));

    $created = User::where('email', 'nuevo@pharmacy.hn')->first();
    expect($created)->not->toBeNull()
        ->and($created->must_change_password)->toBeTrue()
        ->and($created->hasRole('Cajero'))->toBeTrue();
});

test('create validates required fields', function () {
    loginAs('Administrador');

    Livewire::test(Create::class)
        ->call('save')
        ->assertHasErrors(['name', 'email', 'password', 'role']);
});

test('create validates unique email', function () {
    loginAs('Administrador');
    User::factory()->create(['email' => 'taken@pharmacy.hn']);

    Livewire::test(Create::class)
        ->set('name', 'Test User')
        ->set('email', 'taken@pharmacy.hn')
        ->set('password', 'secret-password-123')
        ->set('password_confirmation', 'secret-password-123')
        ->set('role', 'Cajero')
        ->call('save')
        ->assertHasErrors(['email']);
});
