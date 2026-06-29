<?php

use App\Models\Product;

it('rechaza el acceso a notificaciones sin token (401)', function () {
    $this->getJson('/api/notifications')->assertUnauthorized();
});

it('devuelve las alertas con estructura {data, count}', function () {
    apiAs('Cajero');
    Product::factory()->create(['stock' => 3, 'expiration_date' => now()->addYear()]);

    $res = $this->getJson('/api/notifications')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['type', 'label', 'count', 'variant']],
            'count',
        ]);

    expect($res->json('count'))->toBe(count($res->json('data')));
});

it('filtra productos vencidos con ?expired=1', function () {
    apiAs('Administrador');
    Product::factory()->expired()->create(['name' => 'Vencido X', 'sku' => 'VENC-1']);
    Product::factory()->create(['name' => 'Vigente Y', 'sku' => 'VIG-1', 'expiration_date' => now()->addYear()]);

    $names = collect($this->getJson('/api/products?expired=1')->assertOk()->json('data'))->pluck('name');

    expect($names)->toContain('Vencido X')
        ->and($names)->not->toContain('Vigente Y');
});
