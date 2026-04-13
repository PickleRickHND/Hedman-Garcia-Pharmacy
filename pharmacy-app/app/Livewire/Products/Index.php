<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Inventario')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'filter', history: true)]
    public string $stockFilter = '';

    #[Url(as: 'cat', history: true)]
    public string $categoryFilter = '';

    #[Url(as: 'sort', history: true)]
    public string $sortField = 'name';

    #[Url(as: 'dir', history: true)]
    public string $sortDirection = 'asc';

    public ?string $flashMessage = null;
    public ?string $flashVariant = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStockFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteProduct(int $productId): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $product = Product::findOrFail($productId);
        $name = $product->name;
        $product->delete();

        $this->flashVariant = 'success';
        $this->flashMessage = "Producto «{$name}» eliminado (soft delete).";
    }

    public function render(): View
    {
        $query = Product::query()
            ->with('category')
            ->search($this->search)
            ->when($this->categoryFilter, fn ($q) => $q->byCategory((int) $this->categoryFilter))
            ->when($this->stockFilter === 'low', fn ($q) => $q->lowStock())
            ->when($this->stockFilter === 'out', fn ($q) => $q->outOfStock())
            ->when($this->stockFilter === 'expiring', fn ($q) => $q->expiringSoon())
            ->when($this->stockFilter === 'expired', fn ($q) => $q->expired());

        $allowedSorts = ['name', 'sku', 'stock', 'price', 'expiration_date'];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'name';

        $products = $query
            ->orderBy($sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'summary' => [
                'total' => Product::count(),
                'low_stock' => Product::lowStock()->count(),
                'expiring' => Product::expiringSoon()->count(),
                'expired' => Product::expired()->count(),
            ],
        ]);
    }
}
