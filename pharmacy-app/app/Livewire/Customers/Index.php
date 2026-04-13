<?php

declare(strict_types=1);

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Clientes')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

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

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $customer = Customer::findOrFail($id);
        $name = $customer->name;
        $customer->delete();

        $this->flashVariant = 'success';
        $this->flashMessage = "Cliente «{$name}» eliminado.";
    }

    public function render(): View
    {
        $customers = Customer::query()
            ->withCount('invoices')
            ->search($this->search)
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.customers.index', [
            'customers' => $customers,
        ]);
    }
}
