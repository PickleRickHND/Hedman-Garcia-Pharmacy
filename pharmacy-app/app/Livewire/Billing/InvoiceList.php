<?php

declare(strict_types=1);

namespace App\Livewire\Billing;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Facturación')]
class InvoiceList extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'pm', history: true)]
    public string $paymentMethodFilter = '';

    #[Url(as: 'date', history: true)]
    public string $dateFilter = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPaymentMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $invoices = Invoice::query()
            ->with(['seller', 'paymentMethod'])
            ->when($this->search, function ($q) {
                $like = '%'.$this->search.'%';
                $q->where(function ($inner) use ($like) {
                    $inner->where('invoice_number', 'like', $like)
                        ->orWhere('customer_name', 'like', $like)
                        ->orWhere('customer_rtn', 'like', $like);
                });
            })
            ->when($this->paymentMethodFilter, fn ($q) => $q->where('payment_method_id', $this->paymentMethodFilter))
            ->when($this->dateFilter === 'today', fn ($q) => $q->whereDate('issued_at', today()))
            ->when($this->dateFilter === 'week', fn ($q) => $q->whereBetween('issued_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($this->dateFilter === 'month', fn ($q) => $q->whereBetween('issued_at', [now()->startOfMonth(), now()->endOfMonth()]))
            ->orderByDesc('issued_at')
            ->paginate(15);

        return view('livewire.billing.invoice-list', [
            'invoices' => $invoices,
            'paymentMethods' => PaymentMethod::active()->orderBy('name')->get(),
            'summary' => [
                'today_count' => Invoice::emitted()->forToday()->count(),
                'today_revenue' => (float) Invoice::emitted()->forToday()->sum('total'),
                'week_revenue' => (float) Invoice::emitted()
                    ->whereBetween('issued_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->sum('total'),
            ],
        ]);
    }
}
