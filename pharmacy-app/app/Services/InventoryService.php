<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class InventoryService
{
    /**
     * Ajusta el stock de un producto de forma atomica y registra el movimiento en el kardex.
     * Un delta positivo suma (reposicion); negativo resta (venta/merma).
     *
     * @throws RuntimeException cuando el stock resultante seria negativo
     */
    public function adjustStock(
        Product $product,
        int $delta,
        string $type = StockMovement::TYPE_ADJUSTMENT,
        ?string $referenceType = null,
        ?int $referenceId = null,
        string $reason = '',
    ): Product {
        if ($delta === 0) {
            throw new InvalidArgumentException('Delta no puede ser cero.');
        }

        return DB::transaction(function () use ($product, $delta, $type, $referenceType, $referenceId, $reason): Product {
            $fresh = Product::lockForUpdate()->findOrFail($product->id);

            $stockBefore = $fresh->stock;
            $newStock = $stockBefore + $delta;

            if ($newStock < 0) {
                throw new RuntimeException(
                    "Stock insuficiente para {$fresh->name}. Disponible: {$fresh->stock}, solicitado: ".abs($delta)
                );
            }

            $fresh->update(['stock' => $newStock]);

            StockMovement::create([
                'product_id' => $fresh->id,
                'user_id' => auth()->id() ?? 1,
                'type' => $type,
                'quantity' => $delta,
                'stock_before' => $stockBefore,
                'stock_after' => $newStock,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reason' => $reason ?: null,
            ]);

            return $fresh;
        });
    }

    /**
     * Decrementa stock (venta/consumo). Atajo semantico sobre adjustStock.
     */
    public function decrement(
        Product $product,
        int $quantity,
        string $type = StockMovement::TYPE_SALE,
        ?string $referenceType = null,
        ?int $referenceId = null,
        string $reason = 'venta',
    ): Product {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a cero.');
        }

        return $this->adjustStock($product, -$quantity, $type, $referenceType, $referenceId, $reason);
    }

    /**
     * Incrementa stock (reposicion).
     */
    public function increment(
        Product $product,
        int $quantity,
        string $type = StockMovement::TYPE_PURCHASE,
        ?string $referenceType = null,
        ?int $referenceId = null,
        string $reason = 'reposicion',
    ): Product {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a cero.');
        }

        return $this->adjustStock($product, $quantity, $type, $referenceType, $referenceId, $reason);
    }
}
