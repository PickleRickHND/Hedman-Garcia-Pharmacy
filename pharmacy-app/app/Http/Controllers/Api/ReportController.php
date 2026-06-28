<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Reporte de ventas por periodo (default: mes en curso).
     */
    public function sales(Request $request, ReportService $reports): JsonResponse
    {
        [$from, $to] = $this->resolvePeriod($request);

        return response()->json(['data' => $reports->salesByPeriod($from, $to)]);
    }

    /**
     * Top productos por cantidad o por ingresos.
     */
    public function products(Request $request, ReportService $reports): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'in:quantity,revenue'],
        ]);

        [$from, $to] = $this->resolvePeriod($request);

        $top = $reports->topProducts(
            $from,
            $to,
            $validated['limit'] ?? 10,
            $validated['sort_by'] ?? 'quantity',
        );

        return response()->json(['data' => $top]);
    }

    /**
     * Snapshot del inventario actual.
     */
    public function inventory(ReportService $reports): JsonResponse
    {
        return response()->json(['data' => $reports->inventorySnapshot()]);
    }

    /**
     * Resuelve el rango [from, to] desde el request, con default al mes en curso.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolvePeriod(Request $request): array
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->filled('from') ? Carbon::parse($request->string('from')->toString()) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->string('to')->toString()) : now();

        return [$from, $to];
    }
}
