<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Reporte de ventas por periodo.
     */
    public function salesByPeriod(Carbon $from, Carbon $to): array
    {
        $invoices = Invoice::emitted()
            ->whereBetween('issued_at', [$from->startOfDay(), $to->endOfDay()])
            ->get();

        $days = max($from->diffInDays($to) + 1, 1);

        $byPaymentMethod = $invoices->groupBy('payment_method_id')
            ->map(function ($group) {
                $pm = $group->first()->paymentMethod;

                return [
                    'method' => $pm?->name ?? 'Desconocido',
                    'count' => $group->count(),
                    'total' => round((float) $group->sum('total'), 2),
                ];
            })
            ->values();

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_invoices' => $invoices->count(),
            'total_revenue' => round((float) $invoices->sum('total'), 2),
            'total_discount' => round((float) $invoices->sum('discount_total'), 2),
            'total_tax' => round((float) $invoices->sum('tax'), 2),
            'daily_average' => round((float) $invoices->sum('total') / $days, 2),
            'by_payment_method' => $byPaymentMethod,
        ];
    }

    /**
     * Top N productos por cantidad vendida o por ingresos.
     */
    public function topProducts(Carbon $from, Carbon $to, int $limit = 10, string $sortBy = 'quantity'): Collection
    {
        $column = $sortBy === 'revenue' ? DB::raw('SUM(invoice_items.subtotal)') : DB::raw('SUM(invoice_items.quantity)');

        return InvoiceItem::query()
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', Invoice::STATUS_EMITTED)
            ->whereBetween('invoices.issued_at', [$from->startOfDay(), $to->endOfDay()])
            ->select([
                'invoice_items.product_id',
                'invoice_items.product_name',
                'invoice_items.product_sku',
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.subtotal) as total_revenue'),
            ])
            ->groupBy('invoice_items.product_id', 'invoice_items.product_name', 'invoice_items.product_sku')
            ->orderByDesc($sortBy === 'revenue' ? 'total_revenue' : 'total_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Snapshot del inventario actual.
     */
    public function inventorySnapshot(): array
    {
        $products = Product::with('category')->orderBy('name')->get();

        $totalValue = $products->sum(fn ($p) => (float) $p->price * $p->stock);

        return [
            'total_products' => $products->count(),
            'total_units' => $products->sum('stock'),
            'total_value' => round($totalValue, 2),
            'low_stock' => Product::lowStock()->count(),
            'out_of_stock' => Product::outOfStock()->count(),
            'expired' => Product::expired()->count(),
            'expiring_soon' => Product::expiringSoon()->count(),
            'products' => $products,
        ];
    }
}
