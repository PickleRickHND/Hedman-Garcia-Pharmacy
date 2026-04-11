<?php

use App\Livewire\Products\Create;
use App\Models\Product;
use Livewire\Livewire;

test('cashier cannot access create form', function () {
    loginAs('Cajero');

    $this->get(route('products.create'))->assertForbidden();
});

test('admin can access create form', function () {
    loginAs('Administrador');

    $this->get(route('products.create'))->assertOk();
});

test('admin creates product with valid data', function () {
    loginAs('Administrador');

    Livewire::test(Create::class)
        ->set('sku', 'NEW001')
        ->set('name', 'Producto Nuevo')
        ->set('description', 'Descripción del producto nuevo')
        ->set('stock', 50)
        ->set('price', '99.99')
        ->set('expiration_date', now()->addYear()->format('Y-m-d'))
        ->set('presentation', 'Tableta 500mg')
        ->set('administration_form', 'Oral')
        ->set('storage', 'Temperatura ambiente')
        ->set('packaging', 'Caja 20 und')
        ->call('save')
        ->assertRedirect(route('products.index'));

    $created = Product::where('sku', 'NEW001')->first();
    expect($created)->not->toBeNull()
        ->and($created->name)->toBe('Producto Nuevo')
        ->and($created->stock)->toBe(50);
});

test('create validates required fields', function () {
    loginAs('Administrador');

    Livewire::test(Create::class)
        ->set('price', '')
        ->call('save')
        ->assertHasErrors(['sku', 'name', 'price']);
});

test('create rejects duplicate sku', function () {
    loginAs('Administrador');
    Product::factory()->create(['sku' => 'TAKEN']);

    Livewire::test(Create::class)
        ->set('sku', 'TAKEN')
        ->set('name', 'Another Product')
        ->set('stock', 10)
        ->set('price', '50')
        ->call('save')
        ->assertHasErrors(['sku']);
});

test('create rejects negative stock', function () {
    loginAs('Administrador');

    Livewire::test(Create::class)
        ->set('sku', 'NEG001')
        ->set('name', 'Negativo')
        ->set('stock', -5)
        ->set('price', '50')
        ->call('save')
        ->assertHasErrors(['stock']);
});

test('create rejects past expiration date', function () {
    loginAs('Administrador');

    Livewire::test(Create::class)
        ->set('sku', 'PAST001')
        ->set('name', 'Vencido')
        ->set('stock', 10)
        ->set('price', '50')
        ->set('expiration_date', now()->subDay()->format('Y-m-d'))
        ->call('save')
        ->assertHasErrors(['expiration_date']);
});
