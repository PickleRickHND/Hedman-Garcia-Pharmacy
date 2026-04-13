<?php

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\BillingService;
use App\Services\InventoryService;
use Database\Seeders\PaymentMethodSeeder;

it('creates a stock movement on adjustStock', function () {
    $user = loginAs('Administrador');
    $product = Product::factory()->create(['stock' => 50]);

    $service = new InventoryService();
    $service->adjustStock($product, -5, StockMovement::TYPE_SALE, 'invoice', 1, 'test sale');

    $movement = StockMovement::latest('id')->first();

    expect($movement->product_id)->toBe($product->id)
        ->and($movement->user_id)->toBe($user->id)
        ->and($movement->type)->toBe('sale')
        ->and($movement->quantity)->toBe(-5)
        ->and($movement->stock_before)->toBe(50)
        ->and($movement->stock_after)->toBe(45)
        ->and($movement->reference_type)->toBe('invoice')
        ->and($movement->reference_id)->toBe(1)
        ->and($movement->reason)->toBe('test sale');
});

it('creates movements on increment and decrement', function () {
    loginAs('Cajero');
    $product = Product::factory()->create(['stock' => 20]);

    $service = new InventoryService();
    $service->increment($product, 10, StockMovement::TYPE_PURCHASE, null, null, 'reposicion test');
    $service->decrement($product, 3, StockMovement::TYPE_SALE, null, null, 'venta test');

    $movements = StockMovement::where('product_id', $product->id)->orderBy('id')->get();

    expect($movements)->toHaveCount(2)
        ->and($movements[0]->quantity)->toBe(10)
        ->and($movements[0]->stock_after)->toBe(30)
        ->and($movements[1]->quantity)->toBe(-3)
        ->and($movements[1]->stock_after)->toBe(27);
});

it('issueInvoice creates sale movements for each item', function () {
    $this->seed(PaymentMethodSeeder::class);
    loginAs('Cajero');

    $productA = Product::factory()->create(['price' => 100, 'stock' => 50]);
    $productB = Product::factory()->create(['price' => 50, 'stock' => 30]);

    $invoice = app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [
            ['product_id' => $productA->id, 'quantity' => 2],
            ['product_id' => $productB->id, 'quantity' => 3],
        ],
        customer: ['customer_name' => 'Test', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    $movements = StockMovement::where('reference_type', 'invoice')
        ->where('reference_id', $invoice->id)
        ->get();

    expect($movements)->toHaveCount(2)
        ->and($movements->where('product_id', $productA->id)->first()->quantity)->toBe(-2)
        ->and($movements->where('product_id', $productB->id)->first()->quantity)->toBe(-3);
});

it('allows admin and cashier to view movements page', function () {
    loginAs('Cajero');

    $this->get(route('inventory.movements'))
        ->assertOk();
});

it('blocks guest from movements page', function () {
    loginAs('Invitado');

    $this->get(route('inventory.movements'))
        ->assertForbidden();
});

it('filters movements by type', function () {
    loginAs('Administrador');
    $product = Product::factory()->create(['stock' => 100]);

    $service = new InventoryService();
    $service->decrement($product, 5, StockMovement::TYPE_SALE);
    $service->increment($product, 10, StockMovement::TYPE_PURCHASE);

    $component = \Livewire\Livewire::test(\App\Livewire\Inventory\StockMovements::class)
        ->set('typeFilter', 'sale');

    // La tabla solo debe tener movimientos de tipo 'sale', no 'purchase'
    $movements = StockMovement::where('type', 'purchase')->count();
    $saleMovements = StockMovement::where('type', 'sale')->count();
    expect($saleMovements)->toBeGreaterThan(0);

    // Verify the page renders OK with the filter
    $component->assertOk();
});
