<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Services\ReportService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Reporte de inventario')]
class InventoryReport extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
    }

    #[Computed]
    public function snapshot(): array
    {
        return app(ReportService::class)->inventorySnapshot();
    }

    public function render(): View
    {
        return view('livewire.reports.inventory');
    }
}
