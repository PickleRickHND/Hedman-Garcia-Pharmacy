<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CashRegister
 */
class CashRegisterResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'is_open' => $this->status === CashRegister::STATUS_OPEN,
            'opened_at' => $this->opened_at?->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
            'opening_amount' => $this->opening_amount,
            'expected_amount' => $this->expected_amount,
            'actual_amount' => $this->actual_amount,
            'difference' => $this->difference,
            'invoices_count' => $this->invoices_count,
            'voided_count' => $this->voided_count,
            'total_sales' => $this->total_sales,
            'total_cash' => $this->total_cash,
            'total_card' => $this->total_card,
            'total_transfer' => $this->total_transfer,
            'notes' => $this->notes,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
