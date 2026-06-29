<?php

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\BillingService;
use Database\Seeders\PaymentMethodSeeder;

beforeEach(function () {
    $this->seed(PaymentMethodSeeder::class);
});

/** Emite una factura real para poblar los reportes. */
function seedSale(App\Models\User $seller, int $quantity = 2, float $price = 100): Product
{
    $product = Product::factory()->create(['price' => $price, 'stock' => 100]);

    app(BillingService::class)->issueInvoice(
        seller: $seller,
        lineItems: [['product_id' => $product->id, 'quantity' => $quantity]],
        customer: ['customer_name' => 'Cliente Test', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    return $product;
}

// ------------------------------------------------------------------
// Autorización (reportes: solo Administrador)
// ------------------------------------------------------------------

it('rechaza reportes sin token (401)', function () {
    $this->getJson('/api/reports/sales')->assertUnauthorized();
});

it('rechaza reportes a un Cajero (403)', function () {
    apiAs('Cajero');

    $this->getJson('/api/reports/sales')->assertForbidden();
    $this->getJson('/api/reports/products')->assertForbidden();
    $this->getJson('/api/reports/inventory')->assertForbidden();
});

// ------------------------------------------------------------------
// Ventas
// ------------------------------------------------------------------

it('devuelve el reporte de ventas con totales y desglose por método de pago', function () {
    $admin = apiAs('Administrador');
    seedSale($admin, quantity: 2, price: 100);

    $this->getJson('/api/reports/sales')
        ->assertOk()
        ->assertJsonStructure(['data' => [
            'from', 'to', 'total_invoices', 'total_revenue', 'total_tax',
            'total_discount', 'daily_average', 'by_payment_method',
        ]])
        ->assertJsonPath('data.total_invoices', 1);
});

// ------------------------------------------------------------------
// Top productos — incluye regresión del fix de `limit` (string → int)
// ------------------------------------------------------------------

it('devuelve top productos sin error al pasar limit por query string (regresión)', function () {
    $admin = apiAs('Administrador');
    seedSale($admin, quantity: 3, price: 100);

    // Antes del fix, limit como string disparaba TypeError (500).
    $this->getJson('/api/reports/products?limit=5&sort_by=revenue')
        ->assertOk()
        ->assertJsonStructure(['data' => [['product_id', 'product_name', 'total_quantity', 'total_revenue']]]);
});

it('ordena el top de productos por cantidad', function () {
    $admin = apiAs('Administrador');
    $product = seedSale($admin, quantity: 4, price: 50);

    $data = $this->getJson('/api/reports/products?sort_by=quantity&limit=10')->assertOk()->json('data');

    expect($data[0]['product_id'])->toBe($product->id)
        ->and((int) $data[0]['total_quantity'])->toBe(4);
});

it('valida sort_by inválido (422)', function () {
    apiAs('Administrador');

    $this->getJson('/api/reports/products?sort_by=invalido')
        ->assertStatus(422)
        ->assertJsonValidationErrors(['sort_by']);
});

// ------------------------------------------------------------------
// Inventario
// ------------------------------------------------------------------

it('devuelve el snapshot de inventario', function () {
    apiAs('Administrador');
    Product::factory()->count(3)->create(['stock' => 20]);

    $this->getJson('/api/reports/inventory')
        ->assertOk()
        ->assertJsonStructure(['data' => [
            'total_products', 'total_units', 'total_value',
            'low_stock', 'out_of_stock', 'expired', 'expiring_soon', 'products',
        ]])
        ->assertJsonPath('data.total_products', 3);
});
