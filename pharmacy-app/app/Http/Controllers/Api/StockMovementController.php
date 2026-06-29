<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockMovementController extends Controller
{
    /**
     * Kardex de movimientos de stock (solo lectura) con filtros por producto,
     * tipo y rango de fechas — reusa los scopes del modelo StockMovement.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $movements = StockMovement::query()
            ->with(['product', 'user'])
            ->when($request->filled('product_id'), fn ($q) => $q->forProduct($request->integer('product_id')))
            ->when($request->filled('type'), fn ($q) => $q->ofType($request->string('type')->toString()))
            ->dateRange($request->input('date_from'), $request->input('date_to'))
            ->latest('created_at')
            ->paginate($request->integer('per_page', 20));

        return StockMovementResource::collection($movements);
    }
}
