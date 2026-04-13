<?php

declare(strict_types=1);

namespace App\Livewire\Inventory;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kardex de inventario')]
class StockMovements extends Component
{
    use WithPagination;

    #[Url(as: 'product', history: true)]
    public string $productFilter = '';

    #[Url(as: 'type', history: true)]
    public string $typeFilter = '';

    #[Url(as: 'from', history: true)]
    public string $dateFrom = '';

    #[Url(as: 'to', history: true)]
    public string $dateTo = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);
    }

    public function updatingProductFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $movements = StockMovement::query()
            ->with(['product', 'user'])
            ->when($this->productFilter, fn ($q) => $q->forProduct((int) $this->productFilter))
            ->when($this->typeFilter, fn ($q) => $q->ofType($this->typeFilter))
            ->dateRange($this->dateFrom ?: null, $this->dateTo ?: null)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('livewire.inventory.stock-movements', [
            'movements' => $movements,
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku']),
            'types' => StockMovement::TYPE_LABELS,
        ]);
    }
}
