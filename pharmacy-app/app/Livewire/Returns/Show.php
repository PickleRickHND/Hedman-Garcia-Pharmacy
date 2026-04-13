<?php

declare(strict_types=1);

namespace App\Livewire\Returns;

use App\Models\ReturnOrder;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detalle de devolución')]
class Show extends Component
{
    public ReturnOrder $returnOrder;

    public function mount(ReturnOrder $returnOrder): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);

        $this->returnOrder = $returnOrder->load(['items.product', 'invoice', 'processedBy']);
    }

    public function render(): View
    {
        return view('livewire.returns.show');
    }
}
