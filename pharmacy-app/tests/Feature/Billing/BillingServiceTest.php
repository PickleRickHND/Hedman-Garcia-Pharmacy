<?php

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\BillingService;
use Database\Seeders\PaymentMethodSeeder;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

it('issues an invoice and decrements stock atomically', function () {
    $seller = createUserWithRole('Cajero');
    $productA = Product::factory()->create(['price' => 100, 'stock' => 50]);
    $productB = Product::factory()->create(['price' => 50, 'stock' => 30]);
    $method = PaymentMethod::first();

    $service = app(BillingService::class);

    $invoice = $service->issueInvoice(
        seller: $seller,
        lineItems: [
            ['product_id' => $productA->id, 'quantity' => 2],
            ['product_id' => $productB->id, 'quantity' => 3],
        ],
        customer: ['customer_name' => 'Cliente Test', 'customer_rtn' => null],
        paymentMethod: $method,
    );

    expect((float) $invoice->total)->toEqual(350.00)
        ->and($invoice->invoice_number)->toStartWith('FHG-')
        ->and($invoice->items)->toHaveCount(2)
        ->and($productA->fresh()->stock)->toBe(48)
        ->and($productB->fresh()->stock)->toBe(27);
});

it('rejects empty line items', function () {
    $seller = createUserWithRole('Cajero');

    app(BillingService::class)->issueInvoice(
        seller: $seller,
        lineItems: [],
        customer: ['customer_name' => 'X', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );
})->throws(InvalidArgumentException::class);

it('rejects missing customer name', function () {
    $seller = createUserWithRole('Cajero');
    $product = Product::factory()->create(['stock' => 10]);

    app(BillingService::class)->issueInvoice(
        seller: $seller,
        lineItems: [['product_id' => $product->id, 'quantity' => 1]],
        customer: ['customer_name' => '', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );
})->throws(InvalidArgumentException::class);

it('rolls back when stock is insufficient', function () {
    $seller = createUserWithRole('Cajero');
    $product = Product::factory()->create(['stock' => 2]);

    try {
        app(BillingService::class)->issueInvoice(
            seller: $seller,
            lineItems: [['product_id' => $product->id, 'quantity' => 10]],
            customer: ['customer_name' => 'Cliente', 'customer_rtn' => null],
            paymentMethod: PaymentMethod::first(),
        );
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toContain('Stock insuficiente');
    }

    expect($product->fresh()->stock)->toBe(2);
    expect(\App\Models\Invoice::count())->toBe(0);
});

it('generates sequential invoice numbers', function () {
    $seller = createUserWithRole('Cajero');
    $product = Product::factory()->create(['stock' => 100, 'price' => 10]);
    $method = PaymentMethod::first();

    $first = app(BillingService::class)->issueInvoice(
        seller: $seller,
        lineItems: [['product_id' => $product->id, 'quantity' => 1]],
        customer: ['customer_name' => 'A', 'customer_rtn' => null],
        paymentMethod: $method,
    );

    $second = app(BillingService::class)->issueInvoice(
        seller: $seller,
        lineItems: [['product_id' => $product->id, 'quantity' => 1]],
        customer: ['customer_name' => 'B', 'customer_rtn' => null],
        paymentMethod: $method,
    );

    expect($first->invoice_number)->not->toBe($second->invoice_number);
    expect($first->id)->toBeLessThan($second->id);
});
