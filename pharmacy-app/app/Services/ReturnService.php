<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ReturnItem;
use App\Models\ReturnOrder;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class ReturnService
{
    public function __construct(
        private readonly InventoryService $inventory,
    ) {}

    /**
     * Procesa una devolucion parcial o total de una factura.
     *
     * @param  array<int, array{invoice_item_id:int, quantity:int, restock?:bool}>  $returnItems
     */
    public function processReturn(
        Invoice $invoice,
        User $processedBy,
        string $reason,
        array $returnItems,
    ): ReturnOrder {
        if ($invoice->is_voided) {
            throw new RuntimeException('No se puede devolver una factura anulada.');
        }

        if ($returnItems === []) {
            throw new InvalidArgumentException('Debe seleccionar al menos un producto para devolver.');
        }

        if (blank($reason)) {
            throw new InvalidArgumentException('El motivo de devolución es requerido.');
        }

        return DB::transaction(function () use ($invoice, $processedBy, $reason, $returnItems): ReturnOrder {
            $totalRefund = 0.0;
            $snapshots = [];

            foreach ($returnItems as $line) {
                $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)
                    ->findOrFail($line['invoice_item_id']);

                $alreadyReturned = ReturnItem::where('invoice_item_id', $invoiceItem->id)->sum('quantity');
                $maxReturnable = $invoiceItem->quantity - $alreadyReturned;

                if ($line['quantity'] > $maxReturnable) {
                    throw new RuntimeException(
                        "No se pueden devolver {$line['quantity']} unidades de «{$invoiceItem->product_name}». Máximo devolvible: {$maxReturnable}."
                    );
                }

                $effectiveUnitPrice = $invoiceItem->quantity > 0
                    ? round((float) $invoiceItem->subtotal / $invoiceItem->quantity, 2)
                    : (float) $invoiceItem->unit_price;
                $lineSubtotal = round($effectiveUnitPrice * $line['quantity'], 2);
                $totalRefund = round($totalRefund + $lineSubtotal, 2);

                $snapshots[] = [
                    'invoice_item' => $invoiceItem,
                    'quantity' => $line['quantity'],
                    'unit_price' => $effectiveUnitPrice,
                    'subtotal' => $lineSubtotal,
                    'restock' => $line['restock'] ?? true,
                ];
            }

            $return = ReturnOrder::create([
                'return_number' => $this->nextReturnNumber(),
                'invoice_id' => $invoice->id,
                'user_id' => $processedBy->id,
                'reason' => $reason,
                'total_refund' => round($totalRefund, 2),
                'status' => ReturnOrder::STATUS_COMPLETED,
                'processed_at' => now(),
            ]);

            foreach ($snapshots as $snap) {
                ReturnItem::create([
                    'return_id' => $return->id,
                    'product_id' => $snap['invoice_item']->product_id,
                    'invoice_item_id' => $snap['invoice_item']->id,
                    'quantity' => $snap['quantity'],
                    'unit_price' => $snap['unit_price'],
                    'subtotal' => $snap['subtotal'],
                    'restock' => $snap['restock'],
                ]);

                if ($snap['restock'] && $snap['invoice_item']->product_id) {
                    $product = $snap['invoice_item']->product;
                    if ($product) {
                        $this->inventory->increment(
                            $product,
                            $snap['quantity'],
                            StockMovement::TYPE_RETURN,
                            'return',
                            $return->id,
                            'devolución '.$return->return_number,
                        );
                    }
                }
            }

            return $return->load('items', 'invoice');
        });
    }

    private function nextReturnNumber(): string
    {
        $lastId = ReturnOrder::lockForUpdate()->max('id') ?? 0;

        return 'DEV-'.str_pad((string) ($lastId + 1), 6, '0', STR_PAD_LEFT);
    }
}
