<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CashRegister;
use App\Models\Product;

class NotificationService
{
    /**
     * Alertas de inventario para el componente Livewire legacy (incluye `route`).
     *
     * @return array<int, array{type:string, label:string, count:int, variant:string, route:string}>
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

    /**
     * Alertas operativas para la API/Angular (campana del topbar): agrega agotados
     * y estado de caja, separa stock bajo de agotados, y omite `route` (el frontend
     * mapea `type` a su propia navegación). Calculadas en tiempo real, sin persistir.
     *
     * @return array<int, array{type:string, label:string, count:int, variant:string}>
     */
    public function getOperationalAlerts(): array
    {
        $alerts = [];

        // Stock bajo con existencias (para no solapar con agotados).
        $lowStock = Product::lowStock()->where('stock', '>', 0)->count();
        if ($lowStock > 0) {
            $alerts[] = [
                'type' => 'low_stock',
                'label' => "Stock bajo: {$lowStock} producto".($lowStock > 1 ? 's' : ''),
                'count' => $lowStock,
                'variant' => 'warning',
            ];
        }

        $outOfStock = Product::outOfStock()->count();
        if ($outOfStock > 0) {
            $alerts[] = [
                'type' => 'out_of_stock',
                'label' => "Agotados: {$outOfStock} producto".($outOfStock > 1 ? 's' : ''),
                'count' => $outOfStock,
                'variant' => 'danger',
            ];
        }

        $expired = Product::expired()->count();
        if ($expired > 0) {
            $alerts[] = [
                'type' => 'expired',
                'label' => "Vencidos: {$expired} producto".($expired > 1 ? 's' : ''),
                'count' => $expired,
                'variant' => 'danger',
            ];
        }

        $expiring = Product::expiringSoon()->count();
        if ($expiring > 0) {
            $alerts[] = [
                'type' => 'expiring',
                'label' => "Por vencer: {$expiring} producto".($expiring > 1 ? 's' : ''),
                'count' => $expiring,
                'variant' => 'warning',
            ];
        }

        // Caja: avisa si no hay una caja abierta para operar.
        if (! CashRegister::open()->exists()) {
            $alerts[] = [
                'type' => 'cash_closed',
                'label' => 'No hay una caja abierta',
                'count' => 0,
                'variant' => 'info',
            ];
        }

        return $alerts;
    }
}
