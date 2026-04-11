<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class InventoryService
{
    /**
     * Ajusta el stock de un producto de forma atomica.
     * Un delta positivo suma (reposicion); negativo resta (venta/merma).
     *
     * @throws RuntimeException cuando el stock resultante seria negativo
     */
    public function adjustStock(Product $product, int $delta, string $reason = ''): Product
    {
        if ($delta === 0) {
            throw new InvalidArgumentException('Delta no puede ser cero.');
        }

        return DB::transaction(function () use ($product, $delta): Product {
            $fresh = Product::lockForUpdate()->findOrFail($product->id);

            $newStock = $fresh->stock + $delta;

            if ($newStock < 0) {
                throw new RuntimeException(
                    "Stock insuficiente para {$fresh->name}. Disponible: {$fresh->stock}, solicitado: ".abs($delta)
                );
            }

            $fresh->update(['stock' => $newStock]);

            return $fresh;
        });
    }

    /**
     * Decrementa stock (venta/consumo). Atajo semantico sobre adjustStock.
     */
    public function decrement(Product $product, int $quantity, string $reason = 'venta'): Product
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a cero.');
        }

        return $this->adjustStock($product, -$quantity, $reason);
    }

    /**
     * Incrementa stock (reposicion).
     */
    public function increment(Product $product, int $quantity, string $reason = 'reposicion'): Product
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a cero.');
        }

        return $this->adjustStock($product, $quantity, $reason);
    }
}
