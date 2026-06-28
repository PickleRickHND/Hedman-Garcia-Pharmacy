<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReturnOrder\StoreReturnRequest;
use App\Http\Resources\ReturnResource;
use App\Models\Invoice;
use App\Models\ReturnOrder;
use App\Services\ReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;
use RuntimeException;

class ReturnController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $returns = ReturnOrder::query()
            ->with(['invoice', 'processedBy'])
            ->when($request->filled('invoice_id'), fn ($q) => $q->where('invoice_id', $request->integer('invoice_id')))
            ->latest('processed_at')
            ->paginate($request->integer('per_page', 15));

        return ReturnResource::collection($returns);
    }

    public function show(ReturnOrder $return): ReturnResource
    {
        return new ReturnResource($return->load(['items', 'invoice', 'processedBy']));
    }

    public function store(StoreReturnRequest $request, ReturnService $service): JsonResponse
    {
        $invoice = Invoice::findOrFail($request->integer('invoice_id'));

        $items = array_map(fn (array $item): array => [
            'invoice_item_id' => (int) $item['invoice_item_id'],
            'quantity' => (int) $item['quantity'],
            'restock' => (bool) ($item['restock'] ?? true),
        ], $request->validated('items'));

        try {
            $return = $service->processReturn(
                $invoice,
                $request->user(),
                $request->validated('reason'),
                $items,
            );
        } catch (InvalidArgumentException|RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ReturnResource($return->load(['items', 'invoice', 'processedBy'])))
            ->response()
            ->setStatusCode(201);
    }
}
