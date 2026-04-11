<?php

use App\Livewire\Products\Index;
use App\Models\Product;
use Livewire\Livewire;

test('guests cannot access inventory', function () {
    $this->get(route('products.index'))->assertRedirect('/login');
});

test('invitado cannot access inventory', function () {
    loginAs('Invitado');

    $this->get(route('products.index'))->assertForbidden();
});

test('admin can see inventory', function () {
    loginAs('Administrador');
    Product::factory()->create(['name' => 'Producto Test', 'sku' => 'TEST001']);

    $response = $this->get(route('products.index'));

    $response->assertOk()->assertSee('Producto Test')->assertSee('TEST001');
});

test('cashier can see inventory but not create button', function () {
    loginAs('Cajero');
    Product::factory()->create(['name' => 'Test Prod']);

    $response = $this->get(route('products.index'));

    $response->assertOk()
        ->assertSee('Test Prod')
        ->assertDontSee(route('products.create'));
});

test('admin can search products by name', function () {
    loginAs('Administrador');
    Product::factory()->create(['name' => 'Buscar Este', 'sku' => 'A']);
    Product::factory()->create(['name' => 'Otro Producto', 'sku' => 'B']);

    Livewire::test(Index::class)
        ->set('search', 'Buscar')
        ->assertSee('Buscar Este')
        ->assertDontSee('Otro Producto');
});

test('admin can filter by low stock', function () {
    loginAs('Administrador');
    Product::factory()->create(['name' => 'Con Stock', 'stock' => 100, 'sku' => 'A']);
    Product::factory()->lowStock()->create(['name' => 'Stock Bajo', 'sku' => 'B']);

    Livewire::test(Index::class)
        ->set('stockFilter', 'low')
        ->assertSee('Stock Bajo')
        ->assertDontSee('Con Stock');
});

test('admin can filter by expired', function () {
    loginAs('Administrador');
    Product::factory()->create(['name' => 'Vigente', 'sku' => 'A', 'expiration_date' => now()->addYear()]);
    Product::factory()->expired()->create(['name' => 'Vencido', 'sku' => 'B']);

    Livewire::test(Index::class)
        ->set('stockFilter', 'expired')
        ->assertSee('Vencido')
        ->assertDontSee('Vigente');
});

test('admin can soft delete a product', function () {
    loginAs('Administrador');
    $product = Product::factory()->create(['name' => 'Para Borrar']);

    Livewire::test(Index::class)
        ->call('deleteProduct', $product->id)
        ->assertSet('flashVariant', 'success');

    expect(Product::find($product->id))->toBeNull();
    expect(Product::withTrashed()->find($product->id))->not->toBeNull();
});

test('cashier cannot delete products', function () {
    $cashier = loginAs('Cajero');
    $product = Product::factory()->create();

    // El middleware mount solo exige Admin|Cajero; el guard adicional
    // esta en deleteProduct() via hasRole('Administrador')
    Livewire::test(Index::class)
        ->call('deleteProduct', $product->id)
        ->assertStatus(403);
});
