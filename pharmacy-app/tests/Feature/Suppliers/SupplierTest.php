<?php

use App\Models\Product;
use App\Models\Supplier;

it('allows admin to view suppliers page', function () {
    loginAs('Administrador');

    $this->get(route('suppliers.index'))
        ->assertOk();
});

it('blocks non-admin from suppliers page', function () {
    loginAs('Cajero');

    $this->get(route('suppliers.index'))
        ->assertForbidden();
});

it('allows admin to create a supplier', function () {
    loginAs('Administrador');

    \Livewire\Livewire::test(\App\Livewire\Suppliers\Create::class)
        ->set('name', 'Proveedor Test')
        ->set('phone', '2222-3333')
        ->set('email', 'test@proveedor.hn')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('suppliers.index'));

    $this->assertDatabaseHas('suppliers', ['name' => 'Proveedor Test']);
});

it('validates required name on create', function () {
    loginAs('Administrador');

    \Livewire\Livewire::test(\App\Livewire\Suppliers\Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors('name');
});

it('allows admin to edit a supplier', function () {
    loginAs('Administrador');

    $supplier = Supplier::create(['name' => 'Original']);

    \Livewire\Livewire::test(\App\Livewire\Suppliers\Edit::class, ['supplier' => $supplier])
        ->set('name', 'Actualizado')
        ->set('phone', '9999-0000')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('suppliers.index'));

    expect($supplier->fresh()->name)->toBe('Actualizado');
    expect($supplier->fresh()->phone)->toBe('9999-0000');
});

it('allows admin to toggle supplier active status', function () {
    loginAs('Administrador');

    $supplier = Supplier::create(['name' => 'Toggle Test', 'is_active' => true]);

    \Livewire\Livewire::test(\App\Livewire\Suppliers\Index::class)
        ->call('toggleActive', $supplier->id);

    expect($supplier->fresh()->is_active)->toBeFalse();
});

it('allows admin to delete a supplier and nullifies products', function () {
    loginAs('Administrador');

    $supplier = Supplier::create(['name' => 'Borrar']);
    $product = Product::factory()->create(['supplier_id' => $supplier->id]);

    \Livewire\Livewire::test(\App\Livewire\Suppliers\Index::class)
        ->call('delete', $supplier->id);

    $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    expect($product->fresh()->supplier_id)->toBeNull();
});

it('shows product count per supplier', function () {
    loginAs('Administrador');

    $supplier = Supplier::create(['name' => 'Con Productos']);
    Product::factory()->count(5)->create(['supplier_id' => $supplier->id]);

    \Livewire\Livewire::test(\App\Livewire\Suppliers\Index::class)
        ->assertSee('Con Productos')
        ->assertSee('5');
});
