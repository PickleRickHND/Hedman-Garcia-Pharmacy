<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Reporte de ventas')]
class SalesReport extends Component
{
    public string $dateFrom;
    public string $dateTo;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    #[Computed]
    public function report(): array
    {
        return app(ReportService::class)->salesByPeriod(
            Carbon::parse($this->dateFrom),
            Carbon::parse($this->dateTo),
        );
    }

    public function render(): View
    {
        return view('livewire.reports.sales');
    }
}
