<?php

declare(strict_types=1);

namespace App\Livewire\CashRegister;

use App\Models\CashRegister;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detalle de corte')]
class Show extends Component
{
    public CashRegister $register;

    public function mount(CashRegister $register): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);

        $this->register = $register->load('user');
    }

    public function render(): View
    {
        return view('livewire.cash-register.show');
    }
}
