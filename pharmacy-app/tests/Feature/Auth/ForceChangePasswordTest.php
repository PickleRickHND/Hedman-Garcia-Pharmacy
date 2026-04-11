<?php

use App\Livewire\Auth\ChangePasswordRequired;
use Livewire\Livewire;

test('user with must_change_password is redirected away from dashboard', function () {
    $user = createUserWithRole('Cajero', ['must_change_password' => true]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('password.change-required'));
});

test('user without must_change_password accesses dashboard normally', function () {
    $user = createUserWithRole('Cajero', ['must_change_password' => false]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});

test('user can change password when required', function () {
    $user = createUserWithRole('Cajero', ['must_change_password' => true]);
    $this->actingAs($user);

    Livewire::test(ChangePasswordRequired::class)
        ->set('password', 'a-brand-new-password-123')
        ->set('password_confirmation', 'a-brand-new-password-123')
        ->call('save')
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->must_change_password)->toBeFalse();
});

test('change password required validates confirmation mismatch', function () {
    $user = createUserWithRole('Cajero', ['must_change_password' => true]);
    $this->actingAs($user);

    Livewire::test(ChangePasswordRequired::class)
        ->set('password', 'a-brand-new-password-123')
        ->set('password_confirmation', 'mismatch')
        ->call('save')
        ->assertHasErrors(['password']);
});

test('change-required route is accessible to user flagged', function () {
    $user = createUserWithRole('Cajero', ['must_change_password' => true]);

    $this->actingAs($user)
        ->get(route('password.change-required'))
        ->assertOk();
});
