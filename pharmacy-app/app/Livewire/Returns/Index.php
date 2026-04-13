<?php

declare(strict_types=1);

namespace App\Livewire\Returns;

use App\Models\ReturnOrder;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Devoluciones')]
class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);
    }

    public function render(): View
    {
        $returns = ReturnOrder::with(['invoice', 'processedBy'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.returns.index', [
            'returns' => $returns,
        ]);
    }
}
