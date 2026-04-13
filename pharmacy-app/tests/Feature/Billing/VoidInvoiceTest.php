<?php

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\BillingService;
use Database\Seeders\PaymentMethodSeeder;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

function createInvoiceForVoidTest(int $stock = 50, int $qty = 5): Invoice
{
    $product = Product::factory()->create(['price' => 100, 'stock' => $stock]);

    return app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [['product_id' => $product->id, 'quantity' => $qty]],
        customer: ['customer_name' => 'Test Void', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );
}

it('voids an invoice and restores stock', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForVoidTest(50, 5);
    $product = Product::first();

    expect($product->fresh()->stock)->toBe(45);

    app(BillingService::class)->voidInvoice($invoice, $admin, 'Error en factura');

    $invoice->refresh();
    expect($invoice->status)->toBe(Invoice::STATUS_VOIDED)
        ->and($invoice->voided_by)->toBe($admin->id)
        ->and($invoice->void_reason)->toBe('Error en factura')
        ->and($invoice->voided_at)->not->toBeNull()
        ->and($product->fresh()->stock)->toBe(50);
});

it('creates void movements in kardex', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForVoidTest(50, 3);

    app(BillingService::class)->voidInvoice($invoice, $admin, 'Test kardex');

    $voidMovements = StockMovement::where('type', StockMovement::TYPE_VOID)
        ->where('reference_type', 'invoice')
        ->where('reference_id', $invoice->id)
        ->get();

    expect($voidMovements)->toHaveCount(1)
        ->and($voidMovements->first()->quantity)->toBe(3);
});

it('cannot void an already voided invoice', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForVoidTest();

    app(BillingService::class)->voidInvoice($invoice, $admin, 'Primera vez');
    app(BillingService::class)->voidInvoice($invoice->fresh(), $admin, 'Segunda vez');
})->throws(RuntimeException::class, 'Solo se pueden anular facturas emitidas');

it('requires void reason', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForVoidTest();

    app(BillingService::class)->voidInvoice($invoice, $admin, '');
})->throws(InvalidArgumentException::class);

it('admin can void via UI', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForVoidTest(50, 2);

    \Livewire\Livewire::test(\App\Livewire\Billing\Show::class, ['invoice' => $invoice])
        ->call('openVoidModal')
        ->set('voidReason', 'Cliente pidió anular')
        ->call('confirmVoid')
        ->assertHasNoErrors();

    expect($invoice->fresh()->status)->toBe(Invoice::STATUS_VOIDED);
});

it('cashier cannot void invoice', function () {
    loginAs('Cajero');
    $product = Product::factory()->create(['price' => 100, 'stock' => 50]);

    $invoice = app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [['product_id' => $product->id, 'quantity' => 1]],
        customer: ['customer_name' => 'X', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    \Livewire\Livewire::test(\App\Livewire\Billing\Show::class, ['invoice' => $invoice])
        ->call('openVoidModal')
        ->assertForbidden();
});
