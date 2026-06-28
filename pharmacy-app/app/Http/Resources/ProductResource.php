<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'stock' => $this->stock,
            'price' => $this->price,
            'expiration_date' => $this->expiration_date?->format('Y-m-d'),
            'presentation' => $this->presentation,
            'administration_form' => $this->administration_form,
            'storage' => $this->storage,
            'packaging' => $this->packaging,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'supplier' => $this->whenLoaded('supplier', fn () => [
                'id' => $this->supplier->id,
                'name' => $this->supplier->name,
            ]),
            // Flags de negocio (derivados de los accessors del modelo).
            'is_low_stock' => $this->is_low_stock,
            'is_out_of_stock' => $this->is_out_of_stock,
            'is_expired' => $this->is_expired,
            'is_expiring_soon' => $this->is_expiring_soon,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
