<?php

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\BillingService;
use App\Services\ReturnService;
use Database\Seeders\PaymentMethodSeeder;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

function createInvoiceForReturnTest(int $stock = 50, int $qty = 5): Invoice
{
    $product = Product::factory()->create(['price' => 100, 'stock' => $stock]);

    return app(BillingService::class)->issueInvoice(
        seller: auth()->user(),
        lineItems: [['product_id' => $product->id, 'quantity' => $qty]],
        customer: ['customer_name' => 'Test Return', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );
}

it('processes a partial return and restores stock', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForReturnTest(50, 5);
    $product = Product::first();
    $item = $invoice->items->first();

    expect($product->fresh()->stock)->toBe(45);

    $return = app(ReturnService::class)->processReturn(
        $invoice,
        $admin,
        'Producto defectuoso',
        [['invoice_item_id' => $item->id, 'quantity' => 2, 'restock' => true]],
    );

    expect($return->return_number)->toStartWith('DEV-')
        ->and((float) $return->total_refund)->toEqual(200.00)
        ->and($return->items)->toHaveCount(1)
        ->and($product->fresh()->stock)->toBe(47);
});

it('creates return movements in kardex', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForReturnTest(50, 3);
    $item = $invoice->items->first();

    $return = app(ReturnService::class)->processReturn(
        $invoice,
        $admin,
        'Test kardex',
        [['invoice_item_id' => $item->id, 'quantity' => 1]],
    );

    $movements = StockMovement::where('type', StockMovement::TYPE_RETURN)
        ->where('reference_type', 'return')
        ->where('reference_id', $return->id)
        ->get();

    expect($movements)->toHaveCount(1)
        ->and($movements->first()->quantity)->toBe(1);
});

it('prevents returning more than invoiced', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForReturnTest(50, 3);
    $item = $invoice->items->first();

    app(ReturnService::class)->processReturn(
        $invoice,
        $admin,
        'Primera devolucion',
        [['invoice_item_id' => $item->id, 'quantity' => 99]],
    );
})->throws(RuntimeException::class, 'No se pueden devolver');

it('prevents returning from voided invoice', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForReturnTest(50, 3);

    app(BillingService::class)->voidInvoice($invoice, $admin, 'Anulada');

    app(ReturnService::class)->processReturn(
        $invoice->fresh(),
        $admin,
        'Devolucion invalida',
        [['invoice_item_id' => $invoice->items->first()->id, 'quantity' => 1]],
    );
})->throws(RuntimeException::class, 'No se puede devolver una factura anulada');

it('admin can view returns page', function () {
    loginAs('Administrador');
    $this->get(route('returns.index'))->assertOk();
});

it('admin can access return create form', function () {
    $admin = loginAs('Administrador');
    $invoice = createInvoiceForReturnTest();

    $this->get(route('returns.create', $invoice))->assertOk();
});
