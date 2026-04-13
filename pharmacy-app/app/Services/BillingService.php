<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class BillingService
{
    public function __construct(
        private readonly InventoryService $inventory,
    ) {}

    /**
     * Emite una factura: valida stock, crea Invoice + items, descuenta stock.
     * Todo dentro de una transaccion: si algo falla, rollback completo.
     *
     * @param  array<int, array{product_id:int, quantity:int, discount_percent?:float}>  $lineItems
     * @param  array{customer_name:string, customer_rtn:?string, customer_id?:?int}  $customer
     */
    public function issueInvoice(
        User $seller,
        array $lineItems,
        array $customer,
        PaymentMethod $paymentMethod,
    ): Invoice {
        if ($lineItems === []) {
            throw new InvalidArgumentException('No se puede emitir una factura vacia.');
        }

        if (empty($customer['customer_name'])) {
            throw new InvalidArgumentException('El nombre del cliente es requerido.');
        }

        return DB::transaction(function () use ($seller, $lineItems, $customer, $paymentMethod): Invoice {
            $isvRate = (float) config('pharmacy.tax.isv_rate', 0.15);
            $maxDiscount = (float) config('pharmacy.billing.max_discount_percent', 30);

            $snapshots = [];
            $grossTotal = 0.0;
            $totalDiscountAmount = 0.0;

            foreach ($lineItems as $line) {
                $product = Product::lockForUpdate()->findOrFail($line['product_id']);

                if ($product->stock < $line['quantity']) {
                    throw new RuntimeException(
                        "Stock insuficiente para {$product->name}. Disponible: {$product->stock}, solicitado: {$line['quantity']}"
                    );
                }

                $discountPercent = min($line['discount_percent'] ?? 0, $maxDiscount);
                $discountPercent = max($discountPercent, 0);

                $lineGross = round((float) $product->price * $line['quantity'], 2);
                $lineDiscount = round($lineGross * ($discountPercent / 100), 2);
                $lineSubtotal = round($lineGross - $lineDiscount, 2);

                $grossTotal = round($grossTotal + $lineGross, 2);
                $totalDiscountAmount = round($totalDiscountAmount + $lineDiscount, 2);

                $snapshots[] = [
                    'product' => $product,
                    'quantity' => $line['quantity'],
                    'unit_price' => (float) $product->price,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $lineDiscount,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $totalAfterDiscount = round($grossTotal - $totalDiscountAmount, 2);
            $subtotal = round($totalAfterDiscount / (1 + $isvRate), 2);
            $tax = round($totalAfterDiscount - $subtotal, 2);
            $total = $totalAfterDiscount;

            $invoice = Invoice::create([
                'invoice_number' => $this->nextInvoiceNumber(),
                'user_id' => $seller->id,
                'payment_method_id' => $paymentMethod->id,
                'customer_id' => $customer['customer_id'] ?? null,
                'customer_name' => $customer['customer_name'],
                'customer_rtn' => $customer['customer_rtn'] ?? null,
                'subtotal' => $subtotal,
                'discount_total' => round($totalDiscountAmount, 2),
                'tax' => $tax,
                'total' => $total,
                'status' => Invoice::STATUS_EMITTED,
                'issued_at' => now(),
            ]);

            foreach ($snapshots as $snap) {
                /** @var Product $p */
                $p = $snap['product'];

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $p->id,
                    'product_sku' => $p->sku,
                    'product_name' => $p->name,
                    'quantity' => $snap['quantity'],
                    'unit_price' => $snap['unit_price'],
                    'discount_percent' => $snap['discount_percent'],
                    'discount_amount' => $snap['discount_amount'],
                    'subtotal' => $snap['subtotal'],
                ]);

                $this->inventory->decrement(
                    $p,
                    $snap['quantity'],
                    StockMovement::TYPE_SALE,
                    'invoice',
                    $invoice->id,
                    'venta factura '.$invoice->invoice_number,
                );
            }

            return $invoice->load('items', 'paymentMethod', 'seller');
        });
    }

    /**
     * Anula una factura: revierte el stock de cada item y marca como voided.
     */
    public function voidInvoice(Invoice $invoice, User $voidedBy, string $reason): Invoice
    {
        if ($invoice->status !== Invoice::STATUS_EMITTED) {
            throw new RuntimeException('Solo se pueden anular facturas emitidas.');
        }

        if (blank($reason)) {
            throw new InvalidArgumentException('El motivo de anulación es requerido.');
        }

        return DB::transaction(function () use ($invoice, $voidedBy, $reason): Invoice {
            $invoice->update([
                'status' => Invoice::STATUS_VOIDED,
                'voided_at' => now(),
                'voided_by' => $voidedBy->id,
                'void_reason' => $reason,
            ]);

            foreach ($invoice->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $this->inventory->increment(
                            $product,
                            $item->quantity,
                            StockMovement::TYPE_VOID,
                            'invoice',
                            $invoice->id,
                            'anulación factura '.$invoice->invoice_number,
                        );
                    }
                }
            }

            return $invoice->fresh(['items', 'paymentMethod', 'seller']);
        });
    }

    /**
     * Genera el siguiente numero de factura con prefijo y padding.
     */
    private function nextInvoiceNumber(): string
    {
        $prefix = (string) config('pharmacy.billing.invoice_number_prefix', 'FHG-');
        $padding = (int) config('pharmacy.billing.invoice_number_padding', 6);

        $lastId = Invoice::lockForUpdate()->max('id') ?? 0;

        return $prefix.str_pad((string) ($lastId + 1), $padding, '0', STR_PAD_LEFT);
    }
}
