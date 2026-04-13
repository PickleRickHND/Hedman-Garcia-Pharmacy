<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

class NotificationService
{
    /**
     * Retorna las alertas de inventario calculadas en tiempo real.
     *
     * @return array<int, array{type:string, label:string, count:int, variant:string, route:string, filter:string}>
     */
    public function getAlerts(): array
    {
        $alerts = [];

        $lowStock = Product::lowStock()->count();
        if ($lowStock > 0) {
            $alerts[] = [
                'type' => 'low_stock',
                'label' => "Stock bajo: {$lowStock} producto".($lowStock > 1 ? 's' : ''),
                'count' => $lowStock,
                'variant' => 'warning',
                'route' => route('inventory.index', ['filter' => 'low']),
            ];
        }

        $expired = Product::expired()->count();
        if ($expired > 0) {
            $alerts[] = [
                'type' => 'expired',
                'label' => "Vencidos: {$expired} producto".($expired > 1 ? 's' : ''),
                'count' => $expired,
                'variant' => 'danger',
                'route' => route('inventory.index', ['filter' => 'expired']),
            ];
        }

        $expiring = Product::expiringSoon()->count();
        if ($expiring > 0) {
            $alerts[] = [
                'type' => 'expiring',
                'label' => "Por vencer: {$expiring} producto".($expiring > 1 ? 's' : ''),
                'count' => $expiring,
                'variant' => 'warning',
                'route' => route('inventory.index', ['filter' => 'expiring']),
            ];
        }

        return $alerts;
    }

    public function getTotalCount(): int
    {
        return Product::lowStock()->count()
            + Product::expired()->count()
            + Product::expiringSoon()->count();
    }
}
