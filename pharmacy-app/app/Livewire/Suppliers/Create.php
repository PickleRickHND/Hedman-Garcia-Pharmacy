<?php

declare(strict_types=1);

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Nuevo proveedor')]
class Create extends Component
{
    public string $name = '';
    public string $contact_name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $rtn = '';
    public string $notes = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
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

        Supplier::create($validated);

        session()->flash('suppliers.flash', "Proveedor «{$validated['name']}» creado.");

        return redirect()->route('suppliers.index');
    }

    public function render(): View
    {
        return view('livewire.suppliers.create');
    }
}
