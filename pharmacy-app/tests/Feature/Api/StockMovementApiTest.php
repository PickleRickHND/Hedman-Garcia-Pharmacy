<?php

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;

/** Crea un movimiento de stock de prueba (la tabla no tiene factory). */
function makeMovement(Product $product, User $user, string $type, int $delta, ?string $createdAt = null): StockMovement
{
    $before = $product->stock;
    $movement = StockMovement::create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'type' => $type,
        'quantity' => $delta,
        'stock_before' => $before,
        'stock_after' => $before + $delta,
        'reference_type' => 'invoice',
        'reference_id' => 1,
        'reason' => 'movimiento de prueba',
    ]);

    if ($createdAt !== null) {
        $movement->created_at = $createdAt;
        $movement->save();
    }

    return $movement;
}

// ------------------------------------------------------------------
// Autorización
// ------------------------------------------------------------------

it('rechaza el kardex sin token (401)', function () {
    $this->getJson('/api/stock-movements')->assertUnauthorized();
});

it('permite al Cajero ver el kardex (solo lectura)', function () {
    apiAs('Cajero');

    $this->getJson('/api/stock-movements')->assertOk();
});

it('permite al Administrador ver el kardex', function () {
    apiAs('Administrador');

    $this->getJson('/api/stock-movements')
        ->assertOk()
        ->assertJsonStructure(['data', 'meta']);
});

// ------------------------------------------------------------------
// Estructura y contenido
// ------------------------------------------------------------------

it('devuelve los movimientos con su estructura esperada', function () {
    $user = apiAs('Administrador');
    $product = Product::factory()->create(['stock' => 100]);
    makeMovement($product, $user, StockMovement::TYPE_SALE, -3);

    $this->getJson('/api/stock-movements')
        ->assertOk()
        ->assertJsonStructure(['data' => [[
            'id', 'type', 'type_label', 'quantity', 'stock_before', 'stock_after',
            'reason', 'product' => ['id', 'name', 'sku'], 'user' => ['id', 'name'], 'created_at',
        ]]])
        ->assertJsonPath('data.0.type_label', 'Venta');
});

// ------------------------------------------------------------------
// Filtros
// ------------------------------------------------------------------

it('filtra por tipo de movimiento', function () {
    $user = apiAs('Administrador');
    $product = Product::factory()->create(['stock' => 100]);
    makeMovement($product, $user, StockMovement::TYPE_SALE, -2);
    makeMovement($product, $user, StockMovement::TYPE_PURCHASE, 10);

    $sales = $this->getJson('/api/stock-movements?type=sale')->assertOk()->json('data');
    expect($sales)->toHaveCount(1)
        ->and($sales[0]['type'])->toBe('sale');

    $this->getJson('/api/stock-movements?type=loss')->assertOk()->assertJsonCount(0, 'data');
});

it('filtra por producto', function () {
    $user = apiAs('Administrador');
    $p1 = Product::factory()->create(['stock' => 100]);
    $p2 = Product::factory()->create(['stock' => 100]);
    makeMovement($p1, $user, StockMovement::TYPE_SALE, -1);
    makeMovement($p2, $user, StockMovement::TYPE_SALE, -1);

    $res = $this->getJson("/api/stock-movements?product_id={$p1->id}")->assertOk()->json('data');
    expect($res)->toHaveCount(1)
        ->and($res[0]['product']['id'])->toBe($p1->id);
});

it('filtra por rango de fechas', function () {
    $user = apiAs('Administrador');
    $product = Product::factory()->create(['stock' => 100]);
    makeMovement($product, $user, StockMovement::TYPE_PURCHASE, 5, now()->subDays(10)->toDateTimeString());
    makeMovement($product, $user, StockMovement::TYPE_SALE, -2); // hoy

    $today = now()->toDateString();
    $this->getJson("/api/stock-movements?date_from={$today}")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.type', 'sale');
});
