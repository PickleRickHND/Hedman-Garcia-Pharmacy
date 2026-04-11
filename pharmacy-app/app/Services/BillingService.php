<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentMethod;
use App\Models\Product;
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
     * @param  array<int, array{product_id:int, quantity:int}>  $lineItems
     * @param  array{customer_name:string, customer_rtn:?string}  $customer
     *
     * @throws InvalidArgumentException cuando los items o el customer estan vacios
     * @throws RuntimeException cuando algun item tiene stock insuficiente
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

            // Lock + validar stock en un solo paso
            $snapshots = [];
            $subtotalBeforeTax = 0.0;

            foreach ($lineItems as $line) {
                $product = Product::lockForUpdate()->findOrFail($line['product_id']);

                if ($product->stock < $line['quantity']) {
                    throw new RuntimeException(
                        "Stock insuficiente para {$product->name}. Disponible: {$product->stock}, solicitado: {$line['quantity']}"
                    );
                }

                $lineSubtotal = (float) $product->price * $line['quantity'];
                $subtotalBeforeTax += $lineSubtotal;

                $snapshots[] = [
                    'product' => $product,
                    'quantity' => $line['quantity'],
                    'unit_price' => (float) $product->price,
                    'subtotal' => round($lineSubtotal, 2),
                ];
            }

            $subtotal = round($subtotalBeforeTax / (1 + $isvRate), 2);
            $tax = round($subtotalBeforeTax - $subtotal, 2);
            $total = round($subtotalBeforeTax, 2);

            $invoice = Invoice::create([
                'invoice_number' => $this->nextInvoiceNumber(),
                'user_id' => $seller->id,
                'payment_method_id' => $paymentMethod->id,
                'customer_name' => $customer['customer_name'],
                'customer_rtn' => $customer['customer_rtn'] ?? null,
                'subtotal' => $subtotal,
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
                    'subtotal' => $snap['subtotal'],
                ]);

                $this->inventory->decrement($p, $snap['quantity'], 'venta factura '.$invoice->invoice_number);
            }

            return $invoice->load('items', 'paymentMethod', 'seller');
        });
    }

    /**
     * Genera el siguiente numero de factura con prefijo y padding.
     */
    private function nextInvoiceNumber(): string
    {
        $prefix = (string) config('pharmacy.billing.invoice_number_prefix', 'FHG-');
        $padding = (int) config('pharmacy.billing.invoice_number_padding', 6);

        $lastId = Invoice::max('id') ?? 0;

        return $prefix.str_pad((string) ($lastId + 1), $padding, '0', STR_PAD_LEFT);
    }
}
