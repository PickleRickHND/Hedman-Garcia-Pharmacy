<?php

declare(strict_types=1);

namespace App\Livewire\Returns;

use App\Models\Invoice;
use App\Models\ReturnItem;
use App\Services\ReturnService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
#[Title('Nueva devolución')]
class Create extends Component
{
    public Invoice $invoice;
    public string $reason = '';
    public array $returnItems = [];
    public ?string $flashMessage = null;
    public ?string $flashVariant = null;

    public function mount(Invoice $invoice): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        if ($invoice->is_voided) {
            abort(403, 'No se puede devolver una factura anulada.');
        }

        $this->invoice = $invoice->load('items');

        foreach ($invoice->items as $item) {
            $alreadyReturned = ReturnItem::where('invoice_item_id', $item->id)->sum('quantity');
            $maxReturnable = $item->quantity - $alreadyReturned;

            $this->returnItems[$item->id] = [
                'invoice_item_id' => $item->id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'unit_price' => (float) $item->unit_price,
                'max_qty' => (int) $maxReturnable,
                'quantity' => 0,
                'restock' => true,
            ];
        }
    }

    public function submit(ReturnService $returnService)
    {
        $this->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], attributes: ['reason' => 'motivo de devolución']);

        $items = collect($this->returnItems)
            ->filter(fn ($i) => $i['quantity'] > 0)
            ->map(fn ($i) => [
                'invoice_item_id' => $i['invoice_item_id'],
                'quantity' => (int) $i['quantity'],
                'restock' => $i['restock'],
            ])
            ->values()
            ->all();

        if (empty($items)) {
            $this->flashVariant = 'danger';
            $this->flashMessage = 'Selecciona al menos un producto con cantidad > 0.';
            return;
        }

        try {
            $return = $returnService->processReturn(
                $this->invoice,
                auth()->user(),
                $this->reason,
                $items,
            );
        } catch (RuntimeException $e) {
            $this->flashVariant = 'danger';
            $this->flashMessage = $e->getMessage();
            return;
        }

        session()->flash('returns.flash', "Devolución {$return->return_number} procesada por L. ".number_format((float) $return->total_refund, 2));

        return redirect()->route('returns.show', $return);
    }

    public function render(): View
    {
        return view('livewire.returns.create');
    }
}
