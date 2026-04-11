<?php

use App\Livewire\Users\Index;
use App\Models\User;
use Livewire\Livewire;

test('guests cannot access users index', function () {
    $this->get(route('users.index'))->assertRedirect('/login');
});

test('cashier cannot access users index (403)', function () {
    loginAs('Cajero');

    $this->get(route('users.index'))->assertForbidden();
});

test('admin can see list of users', function () {
    loginAs('Administrador');
    createUserWithRole('Cajero', ['name' => 'Maria Test']);
    createUserWithRole('Invitado', ['name' => 'Invitado Test']);

    $response = $this->get(route('users.index'));

    $response->assertOk()
        ->assertSee('Maria Test')
        ->assertSee('Invitado Test');
});

test('admin can search users by name', function () {
    loginAs('Administrador');
    createUserWithRole('Cajero', ['name' => 'Maria Buscada']);
    createUserWithRole('Cajero', ['name' => 'Otra Persona']);

    Livewire::test(Index::class)
        ->set('search', 'Maria')
        ->assertSee('Maria Buscada')
        ->assertDontSee('Otra Persona');
});

test('admin can filter users by role', function () {
    loginAs('Administrador');
    createUserWithRole('Cajero', ['name' => 'Cajero Test']);
    createUserWithRole('Invitado', ['name' => 'Invitado Test']);

    Livewire::test(Index::class)
        ->set('roleFilter', 'Cajero')
        ->assertSee('Cajero Test')
        ->assertDontSee('Invitado Test');
});

test('admin can force reset another user password', function () {
    $admin = loginAs('Administrador');
    $target = createUserWithRole('Cajero');

    Livewire::test(Index::class)
        ->call('forceReset', $target->id)
        ->assertSet('flashVariant', 'success')
        ->assertSet('generatedPasswordUserId', $target->id);

    expect($target->fresh()->must_change_password)->toBeTrue();
});

test('admin cannot force reset their own password from index', function () {
    $admin = loginAs('Administrador');

    Livewire::test(Index::class)
        ->call('forceReset', $admin->id)
        ->assertSet('flashVariant', 'danger');
});

test('admin can delete another user', function () {
    loginAs('Administrador');
    $victim = createUserWithRole('Cajero', ['name' => 'Victim']);

    Livewire::test(Index::class)
        ->call('deleteUser', $victim->id)
        ->assertSet('flashVariant', 'success');

    expect(User::find($victim->id))->toBeNull();
});

test('admin cannot delete their own account', function () {
    $admin = loginAs('Administrador');

    Livewire::test(Index::class)
        ->call('deleteUser', $admin->id)
        ->assertSet('flashVariant', 'danger');

    expect(User::find($admin->id))->not->toBeNull();
});
