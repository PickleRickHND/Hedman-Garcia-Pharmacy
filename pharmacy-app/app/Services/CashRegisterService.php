<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CashRegister;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CashRegisterService
{
    public function open(User $user, float $openingAmount = 0): CashRegister
    {
        return DB::transaction(function () use ($user, $openingAmount): CashRegister {
            if (CashRegister::lockForUpdate()->open()->exists()) {
                throw new RuntimeException('Ya existe una caja abierta. Ciérrala antes de abrir otra.');
            }

            return CashRegister::create([
                'user_id' => $user->id,
                'opened_at' => now(),
                'opening_amount' => round($openingAmount, 2),
                'status' => CashRegister::STATUS_OPEN,
            ]);
        });
    }

    public function close(CashRegister $register, float $actualAmount, ?string $notes = null): CashRegister
    {
        if ($register->status !== CashRegister::STATUS_OPEN) {
            throw new RuntimeException('Esta caja ya está cerrada.');
        }

        return DB::transaction(function () use ($register, $actualAmount, $notes): CashRegister {
            $invoices = Invoice::emitted()
                ->whereBetween('issued_at', [$register->opened_at, now()])
                ->get();

            $voidedCount = Invoice::voided()
                ->whereBetween('issued_at', [$register->opened_at, now()])
                ->count();

            $totalSales = (float) $invoices->sum('total');

            $totalCash = (float) $invoices->where('payment_method_id', $this->getPaymentMethodId('Efectivo'))->sum('total');
            $totalCard = (float) $invoices
                ->filter(fn ($i) => in_array($i->payment_method_id, [
                    $this->getPaymentMethodId('Tarjeta de Credito'),
                    $this->getPaymentMethodId('Tarjeta de Debito'),
                ]))
                ->sum('total');
            $totalTransfer = (float) $invoices->where('payment_method_id', $this->getPaymentMethodId('Transferencia'))->sum('total');

            $expectedAmount = $register->opening_amount + $totalCash;
            $difference = round($actualAmount - (float) $expectedAmount, 2);

            $register->update([
                'closed_at' => now(),
                'expected_amount' => $expectedAmount,
                'actual_amount' => $actualAmount,
                'difference' => $difference,
                'invoices_count' => $invoices->count(),
                'voided_count' => $voidedCount,
                'total_sales' => round($totalSales, 2),
                'total_cash' => round($totalCash, 2),
                'total_card' => round($totalCard, 2),
                'total_transfer' => round($totalTransfer, 2),
                'notes' => $notes,
                'status' => CashRegister::STATUS_CLOSED,
            ]);

            return $register;
        });
    }

    private function getPaymentMethodId(string $name): ?int
    {
        static $cache = [];

        if (! isset($cache[$name])) {
            $cache[$name] = \App\Models\PaymentMethod::where('name', $name)->value('id');
        }

        return $cache[$name];
    }
}
