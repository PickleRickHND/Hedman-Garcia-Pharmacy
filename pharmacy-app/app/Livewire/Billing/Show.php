<?php

declare(strict_types=1);

namespace App\Livewire\Billing;

use App\Models\Invoice;
use App\Services\BillingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
#[Title('Detalle de factura')]
class Show extends Component
{
    public Invoice $invoice;
    public bool $showVoidModal = false;
    public string $voidReason = '';
    public ?string $flashMessage = null;
    public ?string $flashVariant = null;

    public function mount(Invoice $invoice): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);

        $this->invoice = $invoice->load(['items', 'seller', 'paymentMethod']);
    }

    public function openVoidModal(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
        $this->showVoidModal = true;
        $this->voidReason = '';
    }

    public function cancelVoid(): void
    {
        $this->showVoidModal = false;
        $this->voidReason = '';
    }

    public function confirmVoid(BillingService $billing): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $this->validate([
            'voidReason' => ['required', 'string', 'min:5', 'max:255'],
        ], attributes: ['voidReason' => 'motivo de anulación']);

        try {
            $billing->voidInvoice($this->invoice, auth()->user(), $this->voidReason);
        } catch (RuntimeException $e) {
            $this->flashVariant = 'danger';
            $this->flashMessage = $e->getMessage();
            $this->showVoidModal = false;
            return;
        }

        $this->invoice = $this->invoice->fresh(['items', 'seller', 'paymentMethod', 'voidedByUser']);
        $this->showVoidModal = false;
        $this->flashVariant = 'success';
        $this->flashMessage = "Factura {$this->invoice->invoice_number} anulada. Stock restaurado.";
    }

    public function render(): View
    {
        return view('livewire.billing.show');
    }
}
