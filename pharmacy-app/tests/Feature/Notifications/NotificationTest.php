<?php

use App\Models\Product;
use App\Services\NotificationService;

it('returns zero alerts when inventory is healthy', function () {
    loginAs('Administrador');

    Product::factory()->create(['stock' => 100, 'expiration_date' => now()->addYear()]);

    $service = new NotificationService();
    expect($service->getAlerts())->toBeEmpty()
        ->and($service->getTotalCount())->toBe(0);
});

it('detects low stock products', function () {
    loginAs('Administrador');

    Product::factory()->create(['stock' => 3, 'expiration_date' => now()->addYear()]);

    $service = new NotificationService();
    $alerts = $service->getAlerts();

    expect($alerts)->toHaveCount(1)
        ->and($alerts[0]['type'])->toBe('low_stock')
        ->and($service->getTotalCount())->toBe(1);
});

it('detects expired products', function () {
    loginAs('Administrador');

    Product::factory()->create(['stock' => 100, 'expiration_date' => now()->subDay()]);

    $service = new NotificationService();
    $alerts = $service->getAlerts();

    $types = array_column($alerts, 'type');
    expect($types)->toContain('expired');
});

it('detects expiring soon products', function () {
    loginAs('Administrador');

    Product::factory()->create(['stock' => 100, 'expiration_date' => now()->addDays(15)]);

    $service = new NotificationService();
    $alerts = $service->getAlerts();

    $types = array_column($alerts, 'type');
    expect($types)->toContain('expiring');
});

it('bell component renders with badge count', function () {
    loginAs('Cajero');

    Product::factory()->create(['stock' => 2, 'expiration_date' => now()->addYear()]);
    Product::factory()->create(['stock' => 100, 'expiration_date' => now()->subWeek()]);

    \Livewire\Livewire::test(\App\Livewire\Notifications\Bell::class)
        ->assertOk()
        ->call('toggle')
        ->assertSee('Stock bajo')
        ->assertSee('Vencidos');
});
