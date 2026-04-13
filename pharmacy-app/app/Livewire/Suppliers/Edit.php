<?php

declare(strict_types=1);

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Editar proveedor')]
class Edit extends Component
{
    public Supplier $supplier;
    public string $name = '';
    public string $contact_name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $rtn = '';
    public string $notes = '';

    public function mount(Supplier $supplier): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $this->supplier = $supplier;
        $this->name = $supplier->name;
        $this->contact_name = $supplier->contact_name ?? '';
        $this->phone = $supplier->phone ?? '';
        $this->email = $supplier->email ?? '';
        $this->address = $supplier->address ?? '';
        $this->rtn = $supplier->rtn ?? '';
        $this->notes = $supplier->notes ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'rtn' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $this->supplier->update($validated);

        session()->flash('suppliers.flash', 'Proveedor actualizado.');

        return redirect()->route('suppliers.index');
    }

    public function render(): View
    {
        return view('livewire.suppliers.edit');
    }
}
