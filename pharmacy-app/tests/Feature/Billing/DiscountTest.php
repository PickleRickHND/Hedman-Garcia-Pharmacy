<?php

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\BillingService;
use Database\Seeders\PaymentMethodSeeder;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

it('applies discount to invoice items', function () {
    loginAs('Administrador');

    $product = Product::factory()->create(['price' => 200, 'stock' => 50]);

    $invoice = app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [
            ['product_id' => $product->id, 'quantity' => 2, 'discount_percent' => 10],
        ],
        customer: ['customer_name' => 'Test Descuento', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    // 200 * 2 = 400 gross, 10% off = 40 discount, 360 after discount
    expect((float) $invoice->discount_total)->toEqual(40.00)
        ->and((float) $invoice->total)->toEqual(360.00);

    $item = $invoice->items->first();
    expect((float) $item->discount_percent)->toEqual(10.00)
        ->and((float) $item->discount_amount)->toEqual(40.00)
        ->and((float) $item->subtotal)->toEqual(360.00);
});

it('caps discount at max configured percent', function () {
    loginAs('Administrador');

    $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

    $invoice = app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [
            ['product_id' => $product->id, 'quantity' => 1, 'discount_percent' => 99],
        ],
        customer: ['customer_name' => 'Max Descuento', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    $maxDiscount = config('pharmacy.billing.max_discount_percent', 30);
    $item = $invoice->items->first();
    expect((float) $item->discount_percent)->toEqual($maxDiscount);
});

it('works with zero discount (backwards compatible)', function () {
    loginAs('Cajero');

    $product = Product::factory()->create(['price' => 100, 'stock' => 50]);

    $invoice = app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [
            ['product_id' => $product->id, 'quantity' => 3],
        ],
        customer: ['customer_name' => 'Sin Descuento', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    expect((float) $invoice->discount_total)->toEqual(0.00)
        ->and((float) $invoice->total)->toEqual(300.00);
});

it('stores customer_id on invoice', function () {
    loginAs('Cajero');

    $customer = \App\Models\Customer::create(['name' => 'Linked Customer', 'rtn' => '0801-TEST']);
    $product = Product::factory()->create(['price' => 50, 'stock' => 20]);

    $invoice = app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
        customer: [
            'customer_name' => 'Linked Customer',
            'customer_rtn' => '0801-TEST',
            'customer_id' => $customer->id,
        ],
        paymentMethod: PaymentMethod::first(),
    );

    expect($invoice->customer_id)->toBe($customer->id);
});
