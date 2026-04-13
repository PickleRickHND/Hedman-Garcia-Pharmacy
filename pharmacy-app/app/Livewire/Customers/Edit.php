<?php

declare(strict_types=1);

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Editar cliente')]
class Edit extends Component
{
    public Customer $customer;
    public string $name = '';
    public string $rtn = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $notes = '';

    public function mount(Customer $customer): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);

        $this->customer = $customer;
        $this->name = $customer->name;
        $this->rtn = $customer->rtn ?? '';
        $this->phone = $customer->phone ?? '';
        $this->email = $customer->email ?? '';
        $this->address = $customer->address ?? '';
        $this->notes = $customer->notes ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'rtn' => ['nullable', 'string', 'max:20', Rule::unique('customers', 'rtn')->ignore($this->customer->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $this->customer->update($validated);

        session()->flash('customers.flash', 'Cliente actualizado.');

        return redirect()->route('customers.index');
    }

    public function render(): View
    {
        return view('livewire.customers.edit');
    }
}
