<?php

declare(strict_types=1);

namespace App\Livewire\Billing;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detalle de factura')]
class Show extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);

        $this->invoice = $invoice->load(['items', 'seller', 'paymentMethod']);
    }

    public function render(): View
    {
        return view('livewire.billing.show');
    }
}
