<?php

use App\Livewire\Billing\NewInvoice;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Product;
use Database\Seeders\PaymentMethodSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

test('guests cannot access POS', function () {
    $this->get(route('billing.create'))->assertRedirect('/login');
});

test('invitado cannot access POS', function () {
    loginAs('Invitado');

    $this->get(route('billing.create'))->assertForbidden();
});

test('cajero can access POS', function () {
    loginAs('Cajero');

    $this->get(route('billing.create'))->assertOk();
});

test('add product populates cart', function () {
    loginAs('Cajero');
    $product = Product::factory()->create(['name' => 'Test Prod', 'stock' => 10, 'price' => 100]);

    Livewire::test(NewInvoice::class)
        ->call('addProduct', $product->id)
        ->assertSet('items.0.product_id', $product->id)
        ->assertSet('items.0.quantity', 1);
});

test('adding same product twice increments quantity', function () {
    loginAs('Cajero');
    $product = Product::factory()->create(['stock' => 10]);

    Livewire::test(NewInvoice::class)
        ->call('addProduct', $product->id)
        ->call('addProduct', $product->id)
        ->assertSet('items.0.quantity', 2);
});

test('cannot add more than stock', function () {
    loginAs('Cajero');
    $product = Product::factory()->create(['stock' => 2]);

    $t = Livewire::test(NewInvoice::class)
        ->call('addProduct', $product->id)
        ->call('addProduct', $product->id)
        ->call('addProduct', $product->id);

    expect($t->get('items')[0]['quantity'])->toBe(2);
});

test('out of stock product cannot be added', function () {
    loginAs('Cajero');
    $product = Product::factory()->outOfStock()->create();

    Livewire::test(NewInvoice::class)
        ->call('addProduct', $product->id)
        ->assertCount('items', 0)
        ->assertSet('flashVariant', 'danger');
});

test('decrement item removes when reaches zero', function () {
    loginAs('Cajero');
    $product = Product::factory()->create(['stock' => 10]);

    Livewire::test(NewInvoice::class)
        ->call('addProduct', $product->id)
        ->call('decrementItem', 0)
        ->assertCount('items', 0);
});

test('issue invoice happy path', function () {
    loginAs('Cajero');
    $product = Product::factory()->create(['stock' => 20, 'price' => 100]);
    $method = PaymentMethod::first();

    Livewire::test(NewInvoice::class)
        ->call('addProduct', $product->id)
        ->call('addProduct', $product->id)
        ->set('customer_name', 'Juan Test')
        ->set('payment_method_id', $method->id)
        ->call('issue');

    expect(Invoice::count())->toBe(1);
    $invoice = Invoice::first();
    expect($invoice->customer_name)->toBe('Juan Test')
        ->and((float) $invoice->total)->toEqual(200.00);
    expect($product->fresh()->stock)->toBe(18);
});

test('issue rejects empty cart', function () {
    loginAs('Cajero');

    Livewire::test(NewInvoice::class)
        ->set('customer_name', 'Test')
        ->set('payment_method_id', PaymentMethod::first()->id)
        ->call('issue')
        ->assertHasErrors(['items']);
});

test('issue requires customer name', function () {
    loginAs('Cajero');
    $product = Product::factory()->create(['stock' => 10]);

    Livewire::test(NewInvoice::class)
        ->call('addProduct', $product->id)
        ->set('payment_method_id', PaymentMethod::first()->id)
        ->call('issue')
        ->assertHasErrors(['customer_name']);
});
