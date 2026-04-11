<?php

use App\Models\Product;
use App\Services\InventoryService;

it('decrements stock correctly', function () {
    $product = Product::factory()->create(['stock' => 50]);
    $service = new InventoryService();

    $updated = $service->decrement($product, 10);

    expect($updated->stock)->toBe(40);
});

it('increments stock correctly', function () {
    $product = Product::factory()->create(['stock' => 50]);
    $service = new InventoryService();

    $updated = $service->increment($product, 25);

    expect($updated->stock)->toBe(75);
});

it('throws when decrementing below zero', function () {
    $product = Product::factory()->create(['stock' => 5]);
    $service = new InventoryService();

    $service->decrement($product, 10);
})->throws(RuntimeException::class, 'Stock insuficiente');

it('rejects zero delta', function () {
    $product = Product::factory()->create(['stock' => 5]);
    $service = new InventoryService();

    $service->adjustStock($product, 0);
})->throws(InvalidArgumentException::class);

it('rejects negative quantity on decrement', function () {
    $product = Product::factory()->create(['stock' => 10]);
    $service = new InventoryService();

    $service->decrement($product, -1);
})->throws(InvalidArgumentException::class);
