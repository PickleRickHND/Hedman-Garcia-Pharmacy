<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CashRegister\CloseCashRegisterRequest;
use App\Http\Requests\Api\CashRegister\OpenCashRegisterRequest;
use App\Http\Resources\CashRegisterResource;
use App\Models\CashRegister;
use App\Services\CashRegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class CashRegisterController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $registers = CashRegister::query()
            ->with('user')
            ->latest('opened_at')
            ->paginate($request->integer('per_page', 15));

        return CashRegisterResource::collection($registers);
    }

    /**
     * Caja abierta actual (o null si no hay ninguna).
     */
    public function current(): JsonResponse
    {
        $open = CashRegister::open()->with('user')->first();

        return response()->json([
            'data' => $open ? new CashRegisterResource($open) : null,
        ]);
    }

    public function show(CashRegister $cashRegister): CashRegisterResource
    {
        return new CashRegisterResource($cashRegister->load('user'));
    }

    public function open(OpenCashRegisterRequest $request, CashRegisterService $service): JsonResponse
    {
        try {
            $register = $service->open($request->user(), (float) ($request->validated('opening_amount') ?? 0));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new CashRegisterResource($register->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    public function close(CloseCashRegisterRequest $request, CashRegister $cashRegister, CashRegisterService $service): CashRegisterResource|JsonResponse
    {
        try {
            $register = $service->close(
                $cashRegister,
                (float) $request->validated('actual_amount'),
                $request->validated('notes'),
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new CashRegisterResource($register->load('user'));
    }
}
