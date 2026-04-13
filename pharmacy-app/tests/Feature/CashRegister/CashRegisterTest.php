<?php

use App\Models\CashRegister;
use App\Services\CashRegisterService;
use Database\Seeders\PaymentMethodSeeder;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

it('opens a cash register', function () {
    $user = loginAs('Administrador');

    $register = app(CashRegisterService::class)->open($user, 500.00);

    expect($register->status)->toBe(CashRegister::STATUS_OPEN)
        ->and((float) $register->opening_amount)->toEqual(500.00)
        ->and($register->user_id)->toBe($user->id);
});

it('prevents opening two registers at once', function () {
    $user = loginAs('Administrador');

    app(CashRegisterService::class)->open($user, 0);
    app(CashRegisterService::class)->open($user, 0);
})->throws(RuntimeException::class, 'Ya existe una caja abierta');

it('closes a register with calculated totals', function () {
    $user = loginAs('Cajero');
    $service = app(CashRegisterService::class);

    $register = $service->open($user, 200.00);
    $closed = $service->close($register, 250.00, 'Todo bien');

    expect($closed->status)->toBe(CashRegister::STATUS_CLOSED)
        ->and($closed->closed_at)->not->toBeNull()
        ->and($closed->notes)->toBe('Todo bien');
});

it('prevents closing an already closed register', function () {
    $user = loginAs('Administrador');
    $service = app(CashRegisterService::class);

    $register = $service->open($user, 0);
    $service->close($register, 0);
    $service->close($register, 0);
})->throws(RuntimeException::class, 'ya está cerrada');

it('admin can view cash register page', function () {
    loginAs('Administrador');
    $this->get(route('cash-register.index'))->assertOk();
});

it('cashier can view cash register page', function () {
    loginAs('Cajero');
    $this->get(route('cash-register.index'))->assertOk();
});

it('guest cannot view cash register page', function () {
    loginAs('Invitado');
    $this->get(route('cash-register.index'))->assertForbidden();
});

it('can open register via Livewire', function () {
    loginAs('Cajero');

    \Livewire\Livewire::test(\App\Livewire\CashRegister\Index::class)
        ->set('openingAmount', 300)
        ->call('openRegister')
        ->assertHasNoErrors();

    expect(CashRegister::open()->count())->toBe(1);
});
