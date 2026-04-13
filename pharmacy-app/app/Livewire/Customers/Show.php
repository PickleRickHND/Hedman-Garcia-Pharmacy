<?php

declare(strict_types=1);

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Detalle de cliente')]
class Show extends Component
{
    use WithPagination;

    public Customer $customer;

    public function mount(Customer $customer): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);

        $this->customer = $customer;
    }

    public function render(): View
    {
        $invoices = Invoice::where('customer_id', $this->customer->id)
            ->with(['seller', 'paymentMethod'])
            ->orderByDesc('issued_at')
            ->paginate(10);

        return view('livewire.customers.show', [
            'invoices' => $invoices,
            'totalSpent' => (float) Invoice::where('customer_id', $this->customer->id)->emitted()->sum('total'),
        ]);
    }
}
