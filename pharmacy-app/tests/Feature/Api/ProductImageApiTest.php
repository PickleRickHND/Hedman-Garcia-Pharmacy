<?php

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\BillingService;
use Database\Seeders\PaymentMethodSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ------------------------------------------------------------------
// Subida de imagen en la API de productos
// ------------------------------------------------------------------

it('crea un producto con imagen y expone image_url', function () {
    Storage::fake('public');
    apiAs('Administrador');

    $res = $this->postJson('/api/products', [
        'sku' => 'IMG001',
        'name' => 'Producto con Imagen',
        'stock' => 10,
        'price' => '50.00',
        'image' => UploadedFile::fake()->image('foto.jpg', 300, 300),
    ])->assertCreated();

    $path = Product::where('sku', 'IMG001')->value('image_path');

    expect($path)->not->toBeNull();
    Storage::disk('public')->assertExists($path);
    expect($res->json('data.image_url'))->toContain('storage/'.$path);
});

it('devuelve image_url null cuando el producto no tiene imagen', function () {
    apiAs('Administrador');

    $res = $this->postJson('/api/products', [
        'sku' => 'NOIMG01',
        'name' => 'Sin Imagen',
        'stock' => 5,
        'price' => '20.00',
    ])->assertCreated();

    expect($res->json('data.image_url'))->toBeNull();
});

it('reemplaza la imagen anterior al actualizar', function () {
    Storage::fake('public');
    apiAs('Administrador');

    $product = Product::factory()->create([
        'image_path' => UploadedFile::fake()->image('vieja.jpg')->store('products', 'public'),
    ]);
    $old = $product->image_path;
    Storage::disk('public')->assertExists($old);

    $this->putJson("/api/products/{$product->id}", [
        'sku' => $product->sku,
        'name' => $product->name,
        'stock' => $product->stock,
        'price' => (string) $product->price,
        'image' => UploadedFile::fake()->image('nueva.png', 200, 200),
    ])->assertOk();

    $new = $product->fresh()->image_path;
    expect($new)->not->toBe($old);
    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($new);
});

it('rechaza un archivo que no es imagen', function () {
    apiAs('Administrador');

    $this->postJson('/api/products', [
        'sku' => 'BAD001',
        'name' => 'Archivo Malo',
        'stock' => 1,
        'price' => '10.00',
        'image' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'),
    ])->assertJsonValidationErrors('image');
});

// ------------------------------------------------------------------
// Snapshot de imagen en la factura
// ------------------------------------------------------------------

it('guarda el snapshot de la imagen del producto en el item de factura', function () {
    $this->seed(PaymentMethodSeeder::class);
    $seller = createUserWithRole('Cajero');
    $product = Product::factory()->create(['stock' => 10, 'image_path' => 'products/sku-test.png']);

    $invoice = app(BillingService::class)->issueInvoice(
        seller: $seller,
        lineItems: [['product_id' => $product->id, 'quantity' => 1]],
        customer: ['customer_name' => 'Cliente Test', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    expect($invoice->items->first()->product_image_path)->toBe('products/sku-test.png');
});

it('descarga el PDF de la factura con status 200', function () {
    $this->seed(PaymentMethodSeeder::class);
    $admin = createUserWithRole('Administrador');
    $product = Product::factory()->create(['stock' => 10, 'image_path' => 'products/sku-test.png']);

    $invoice = app(BillingService::class)->issueInvoice(
        seller: $admin,
        lineItems: [['product_id' => $product->id, 'quantity' => 1]],
        customer: ['customer_name' => 'Cliente PDF', 'customer_rtn' => null],
        paymentMethod: PaymentMethod::first(),
    );

    Laravel\Sanctum\Sanctum::actingAs($admin);

    $this->get("/api/invoices/{$invoice->id}/pdf")
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
