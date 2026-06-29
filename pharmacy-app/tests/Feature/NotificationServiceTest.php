<?php

use App\Models\Product;
use App\Services\CashRegisterService;
use App\Services\NotificationService;

it('genera alertas de inventario y de caja sin abrir', function () {
    Product::factory()->create(['stock' => 5, 'expiration_date' => now()->addYear()]);       // stock bajo
    Product::factory()->outOfStock()->create(['expiration_date' => now()->addYear()]);        // agotado
    Product::factory()->expired()->create(['stock' => 50]);                                    // vencido
    Product::factory()->expiringSoon()->create(['stock' => 50]);                               // por vencer

    $types = collect(app(NotificationService::class)->getOperationalAlerts())->pluck('type')->all();

    foreach (['low_stock', 'out_of_stock', 'expired', 'expiring', 'cash_closed'] as $type) {
        expect($types)->toContain($type);
    }
});

it('el stock bajo no incluye los agotados (sin solapar)', function () {
    Product::factory()->outOfStock()->create(['expiration_date' => now()->addYear()]);

    $alerts = collect(app(NotificationService::class)->getOperationalAlerts())->keyBy('type');

    // Solo hay un producto agotado: no debe aparecer alerta de stock bajo.
    expect($alerts->has('out_of_stock'))->toBeTrue()
        ->and($alerts->has('low_stock'))->toBeFalse();
});

it('no genera la alerta de caja cuando hay una caja abierta', function () {
    $user = createUserWithRole('Cajero');
    app(CashRegisterService::class)->open($user, 100);

    $types = collect(app(NotificationService::class)->getOperationalAlerts())->pluck('type')->all();

    expect($types)->not->toContain('cash_closed');
});

it('las alertas operativas incluyen stock bajo y caja sin abrir', function () {
    Product::factory()->create(['stock' => 4, 'expiration_date' => now()->addYear()]);

    // stock bajo + caja sin abrir = 2 alertas operativas (sin caja abierta en el test).
    expect(app(NotificationService::class)->getOperationalAlerts())->toHaveCount(2);
});
