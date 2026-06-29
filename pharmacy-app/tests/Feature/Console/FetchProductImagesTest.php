<?php

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('descarta URLs de imagen fuera de los hosts permitidos (anti-SSRF) y cae al placeholder', function () {
    Storage::fake('public');
    // La API devuelve una thumbnail.source apuntando a un host interno/no permitido.
    Http::fake([
        '*/w/api.php*' => Http::response([
            'query' => ['pages' => [['thumbnail' => ['source' => 'http://169.254.169.254/latest/meta-data/']]]],
        ], 200),
        // Si el comando intentara descargar de ese host, fallaría el test (no debería llamarse).
        '*' => Http::response('SSRF', 200, ['Content-Type' => 'image/png']),
    ]);

    $product = Product::factory()->create(['image_path' => null]);

    $this->artisan('products:fetch-images')->assertSuccessful();

    $product->refresh();
    // No descargó del host malicioso: generó un placeholder local con nombre por id.
    expect($product->image_path)->toBe('products/'.$product->id.'.png');
    Storage::disk('public')->assertExists($product->image_path);
});

it('acepta solo imágenes desde hosts de Wikimedia por https', function () {
    Storage::fake('public');
    // PNG 1x1 válido para que getimagesizefromstring lo acepte.
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
    Http::fake([
        '*/w/api.php*' => Http::response([
            'query' => ['pages' => [['thumbnail' => ['source' => 'https://upload.wikimedia.org/foo.png']]]],
        ], 200),
        'upload.wikimedia.org/*' => Http::response($png, 200, ['Content-Type' => 'image/png']),
    ]);

    $product = Product::factory()->create(['image_path' => null]);

    $this->artisan('products:fetch-images')->assertSuccessful();

    $product->refresh();
    expect($product->image_path)->toStartWith('products/'.$product->id.'.');
    Storage::disk('public')->assertExists($product->image_path);
});
