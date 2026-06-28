<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Api\Invoice\VoidInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;
use RuntimeException;

class InvoiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $invoices = Invoice::query()
            ->with(['seller', 'paymentMethod', 'customer'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $like = '%'.$request->string('search').'%';
                $q->where(function ($inner) use ($like) {
                    $inner->where('invoice_number', 'like', $like)
                        ->orWhere('customer_name', 'like', $like)
                        ->orWhere('customer_rtn', 'like', $like);
                });
            })
            ->when($request->filled('payment_method_id'), fn ($q) => $q->where('payment_method_id', $request->integer('payment_method_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->input('date_filter') === 'today', fn ($q) => $q->whereDate('issued_at', today()))
            ->when($request->input('date_filter') === 'week', fn ($q) => $q->whereBetween('issued_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($request->input('date_filter') === 'month', fn ($q) => $q->whereBetween('issued_at', [now()->startOfMonth(), now()->endOfMonth()]))
            ->latest('issued_at')
            ->paginate($request->integer('per_page', 15));

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request, BillingService $billing): JsonResponse
    {
        $user = $request->user();

        // Paridad con canApplyDiscount(): solo Administrador puede aplicar descuentos.
        $canDiscount = $user->hasRole('Administrador');

        $lineItems = array_map(fn (array $item): array => [
            'product_id' => (int) $item['product_id'],
            'quantity' => (int) $item['quantity'],
            'discount_percent' => $canDiscount ? (float) ($item['discount_percent'] ?? 0) : 0.0,
        ], $request->validated('items'));

        try {
            $invoice = $billing->issueInvoice(
                $user,
                $lineItems,
                [
                    'customer_name' => $request->validated('customer_name'),
                    'customer_rtn' => $request->validated('customer_rtn'),
                    'customer_id' => $request->validated('customer_id'),
                ],
                PaymentMethod::findOrFail($request->integer('payment_method_id')),
            );
        } catch (InvalidArgumentException|RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new InvoiceResource($invoice->load(['items', 'seller', 'paymentMethod', 'customer'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice->load(['items', 'seller', 'paymentMethod', 'customer']));
    }

    public function void(VoidInvoiceRequest $request, Invoice $invoice, BillingService $billing): InvoiceResource|JsonResponse
    {
        try {
            $invoice = $billing->voidInvoice($invoice, $request->user(), $request->validated('reason'));
        } catch (InvalidArgumentException|RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new InvoiceResource($invoice->load(['items', 'seller', 'paymentMethod', 'customer']));
    }
}
