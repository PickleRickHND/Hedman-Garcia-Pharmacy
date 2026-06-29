<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationsController extends Controller
{
    /**
     * Alertas operativas en tiempo real para la campana del topbar.
     * Endpoint liviano (sin métricas) apto para refresco periódico.
     */
    public function __invoke(NotificationService $notifications): JsonResponse
    {
        $alerts = $notifications->getOperationalAlerts();

        return response()->json([
            'data' => $alerts,
            'count' => count($alerts),
        ]);
    }
}
