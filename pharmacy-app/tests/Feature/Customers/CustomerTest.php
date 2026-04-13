<?php

use App\Models\Customer;

it('allows admin to view customers page', function () {
    loginAs('Administrador');
    $this->get(route('customers.index'))->assertOk();
});

it('allows cashier to view customers page', function () {
    loginAs('Cajero');
    $this->get(route('customers.index'))->assertOk();
});

it('blocks guest from customers page', function () {
    loginAs('Invitado');
    $this->get(route('customers.index'))->assertForbidden();
});

it('allows cashier to create a customer', function () {
    loginAs('Cajero');

    \Livewire\Livewire::test(\App\Livewire\Customers\Create::class)
        ->set('name', 'Juan Perez')
        ->set('rtn', '0801-1990-12345')
        ->set('phone', '9999-0000')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('customers.index'));

    $this->assertDatabaseHas('customers', ['name' => 'Juan Perez', 'rtn' => '0801-1990-12345']);
});

it('validates unique RTN', function () {
    loginAs('Cajero');

    Customer::create(['name' => 'Existente', 'rtn' => 'DUPLICADO']);

    \Livewire\Livewire::test(\App\Livewire\Customers\Create::class)
        ->set('name', 'Nuevo')
        ->set('rtn', 'DUPLICADO')
        ->call('save')
        ->assertHasErrors('rtn');
});

it('allows editing a customer', function () {
    loginAs('Cajero');

    $customer = Customer::create(['name' => 'Original']);

    \Livewire\Livewire::test(\App\Livewire\Customers\Edit::class, ['customer' => $customer])
        ->set('name', 'Actualizado')
        ->set('phone', '8888-7777')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('customers.index'));

    expect($customer->fresh()->name)->toBe('Actualizado');
});

it('shows customer detail with invoice count', function () {
    loginAs('Administrador');

    $customer = Customer::create(['name' => 'Cliente Show Test']);

    $this->get(route('customers.show', $customer))
        ->assertOk()
        ->assertSee('Cliente Show Test');
});

it('allows admin to delete a customer', function () {
    loginAs('Administrador');

    $customer = Customer::create(['name' => 'Borrar']);

    \Livewire\Livewire::test(\App\Livewire\Customers\Index::class)
        ->call('delete', $customer->id);

    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
});

it('autocompletes customer in POS', function () {
    loginAs('Cajero');

    Customer::create(['name' => 'Maria Garcia', 'rtn' => '0801-2000-99999']);

    \Livewire\Livewire::test(\App\Livewire\Billing\NewInvoice::class)
        ->set('customerSearch', 'Maria')
        ->assertSee('Maria Garcia');
});
