<?php

declare(strict_types=1);

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Proveedores')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    public ?string $flashMessage = null;
    public ?string $flashVariant = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_active' => ! $supplier->is_active]);

        $this->flashVariant = 'success';
        $this->flashMessage = "Proveedor «{$supplier->name}» ".($supplier->is_active ? 'activado' : 'desactivado').'.';
    }

    public function delete(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $name = $supplier->name;
        $supplier->delete();

        $this->flashVariant = 'success';
        $this->flashMessage = "Proveedor «{$name}» eliminado.";
    }

    public function render(): View
    {
        $suppliers = Supplier::query()
            ->withCount('products')
            ->search($this->search)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.suppliers.index', [
            'suppliers' => $suppliers,
        ]);
    }
}
