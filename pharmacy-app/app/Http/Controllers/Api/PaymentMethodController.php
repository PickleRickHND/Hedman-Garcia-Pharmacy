<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;

class PaymentMethodController extends Controller
{
    /**
     * Métodos de pago activos — consumido por la pantalla de facturación (POS).
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => PaymentMethod::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
