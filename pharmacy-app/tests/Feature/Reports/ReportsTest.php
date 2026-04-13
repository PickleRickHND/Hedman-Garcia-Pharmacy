<?php

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\BillingService;
use App\Services\ReportService;
use Database\Seeders\PaymentMethodSeeder;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

it('salesByPeriod returns correct totals', function () {
    loginAs('Administrador');

    $product = Product::factory()->create(['price' => 100, 'stock' => 50]);

    app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [['product_id' => $product->id, 'quantity' => 3]],
        customer: ['customer_name' => 'Test', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    $report = app(ReportService::class)->salesByPeriod(now()->startOfDay(), now()->endOfDay());

    expect($report['total_invoices'])->toBe(1)
        ->and($report['total_revenue'])->toEqual(300.00);
});

it('topProducts returns ranked results', function () {
    loginAs('Administrador');

    $p1 = Product::factory()->create(['price' => 100, 'stock' => 50, 'name' => 'Product A']);
    $p2 = Product::factory()->create(['price' => 50, 'stock' => 50, 'name' => 'Product B']);

    $service = app(BillingService::class);
    $method = PaymentMethod::first();

    $service->issueInvoice(
        seller: auth()->user(),
        lineItems: [['product_id' => $p1->id, 'quantity' => 10]],
        customer: ['customer_name' => 'X', 'customer_rtn' => null],
        paymentMethod: $method,
    );

    $service->issueInvoice(
        seller: auth()->user(),
        lineItems: [['product_id' => $p2->id, 'quantity' => 3]],
        customer: ['customer_name' => 'Y', 'customer_rtn' => null],
        paymentMethod: $method,
    );

    $top = app(ReportService::class)->topProducts(now()->subDay(), now()->addDay(), 10, 'quantity');

    expect($top)->toHaveCount(2)
        ->and($top->first()->product_name)->toBe('Product A')
        ->and((int) $top->first()->total_quantity)->toBe(10);
});

it('inventorySnapshot returns correct values', function () {
    loginAs('Administrador');

    Product::factory()->create(['price' => 100, 'stock' => 20]);
    Product::factory()->create(['price' => 50, 'stock' => 10]);

    $snapshot = app(ReportService::class)->inventorySnapshot();

    expect($snapshot['total_products'])->toBe(2)
        ->and($snapshot['total_units'])->toBe(30)
        ->and($snapshot['total_value'])->toEqual(2500.00);
});

it('admin can access reports hub', function () {
    loginAs('Administrador');
    $this->get(route('reports.index'))->assertOk();
});

it('cashier cannot access reports', function () {
    loginAs('Cajero');
    $this->get(route('reports.index'))->assertForbidden();
});

it('admin can access sales report', function () {
    loginAs('Administrador');
    $this->get(route('reports.sales'))->assertOk();
});

it('admin can access products report', function () {
    loginAs('Administrador');
    $this->get(route('reports.products'))->assertOk();
});

it('admin can access inventory report', function () {
    loginAs('Administrador');
    $this->get(route('reports.inventory'))->assertOk();
});
