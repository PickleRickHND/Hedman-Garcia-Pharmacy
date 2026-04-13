<?php

declare(strict_types=1);

namespace App\Livewire\CashRegister;

use App\Models\CashRegister;
use App\Services\CashRegisterService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

#[Layout('layouts.app')]
#[Title('Corte de caja')]
class Index extends Component
{
    use WithPagination;

    public float $openingAmount = 0;
    public ?string $flashMessage = null;
    public ?string $flashVariant = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Cajero']), 403);
    }

    public function openRegister(CashRegisterService $service): void
    {
        try {
            $service->open(auth()->user(), $this->openingAmount);
            $this->flashVariant = 'success';
            $this->flashMessage = 'Caja abierta correctamente.';
            $this->openingAmount = 0;
        } catch (RuntimeException $e) {
            $this->flashVariant = 'danger';
            $this->flashMessage = $e->getMessage();
        }
    }

    public function render(): View
    {
        $currentOpen = CashRegister::open()->with('user')->first();
        $history = CashRegister::where('status', CashRegister::STATUS_CLOSED)
            ->with('user')
            ->orderByDesc('closed_at')
            ->paginate(10);

        return view('livewire.cash-register.index', [
            'currentOpen' => $currentOpen,
            'history' => $history,
        ]);
    }
}
