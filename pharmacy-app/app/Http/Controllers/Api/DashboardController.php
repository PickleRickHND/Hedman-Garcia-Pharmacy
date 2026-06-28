<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Métricas del panel principal + alertas de inventario en tiempo real.
     * Disponible para cualquier usuario autenticado (paridad con el componente Dashboard).
     */
    public function __invoke(NotificationService $notifications): JsonResponse
    {
        return response()->json([
            'metrics' => [
                'users_total' => User::count(),
                'users_admins' => User::role('Administrador')->count(),
                'users_cashiers' => User::role('Cajero')->count(),
                'products_total' => Product::count(),
                'low_stock' => Product::lowStock()->count(),
                'expiring_soon' => Product::expiringSoon()->count(),
                'invoices_today' => Invoice::emitted()->forToday()->count(),
                'revenue_today' => (float) Invoice::emitted()->forToday()->sum('total'),
            ],
            'alerts' => $notifications->getAlerts(),
            'alerts_count' => $notifications->getTotalCount(),
        ]);
    }
}
