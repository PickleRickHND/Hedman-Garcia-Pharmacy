<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Reportes')]
class Index extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
    }

    public function render(): View
    {
        return view('livewire.reports.index');
    }
}
