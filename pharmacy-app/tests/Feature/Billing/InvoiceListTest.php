<?php

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Services\BillingService;
use Database\Seeders\PaymentMethodSeeder;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

function createTestInvoice(User $seller, ?string $customerName = null): Invoice
{
    $product = Product::factory()->create(['stock' => 100, 'price' => 100]);

    return app(BillingService::class)->issueInvoice(
        seller: $seller,
        lineItems: [['product_id' => $product->id, 'quantity' => 1]],
        customer: ['customer_name' => $customerName ?? 'Cliente '.uniqid(), 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );
}

test('guests cannot access invoice list', function () {
    $this->get(route('billing.index'))->assertRedirect('/login');
});

test('invitado cannot see invoice list', function () {
    loginAs('Invitado');

    $this->get(route('billing.index'))->assertForbidden();
});

test('cajero can see invoice list', function () {
    $seller = loginAs('Cajero');
    createTestInvoice($seller, 'Cliente Visible');

    $this->get(route('billing.index'))
        ->assertOk()
        ->assertSee('Cliente Visible');
});

test('cajero can view invoice detail', function () {
    $seller = loginAs('Cajero');
    $invoice = createTestInvoice($seller, 'Cliente Detalle');

    $this->get(route('billing.show', $invoice))
        ->assertOk()
        ->assertSee('Cliente Detalle')
        ->assertSee($invoice->invoice_number);
});

test('cajero can download PDF', function () {
    $seller = loginAs('Cajero');
    $invoice = createTestInvoice($seller);

    $response = $this->get(route('billing.pdf', $invoice));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('invitado cannot download PDF', function () {
    $seller = createUserWithRole('Cajero');
    $invoice = createTestInvoice($seller);
    loginAs('Invitado');

    $this->get(route('billing.pdf', $invoice))->assertForbidden();
});
