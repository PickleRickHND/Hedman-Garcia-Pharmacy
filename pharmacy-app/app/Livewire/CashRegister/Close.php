<?php

declare(strict_types=1);

namespace App\Livewire\CashRegister;

use App\Models\CashRegister;
use App\Services\CashRegisterService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
#[Title('Cerrar caja')]
class Close extends Component
{
    public CashRegister $register;
    public string $actualAmount = '0';
    public string $notes = '';
    public ?string $flashMessage = null;
    public ?string $flashVariant = null;

    public function mount(CashRegister $register): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);

        if ($register->status !== CashRegister::STATUS_OPEN) {
            abort(403, 'Esta caja ya fue cerrada.');
        }

        $this->register = $register;
    }

    public function close(CashRegisterService $service)
    {
        $this->validate([
            'actualAmount' => ['required', 'numeric', 'min:0'],
        ], attributes: ['actualAmount' => 'monto real']);

        try {
            $service->close($this->register, (float) $this->actualAmount, $this->notes ?: null);
        } catch (RuntimeException $e) {
            $this->flashVariant = 'danger';
            $this->flashMessage = $e->getMessage();
            return;
        }

        session()->flash('cash.flash', 'Caja cerrada correctamente.');

        return redirect()->route('cash-register.show', $this->register);
    }

    public function render(): View
    {
        return view('livewire.cash-register.close');
    }
}
