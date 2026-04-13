<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Productos más vendidos')]
class ProductsReport extends Component
{
    public string $dateFrom;
    public string $dateTo;
    public string $sortBy = 'quantity';
    public int $limit = 10;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    #[Computed]
    public function topProducts(): Collection
    {
        return app(ReportService::class)->topProducts(
            Carbon::parse($this->dateFrom),
            Carbon::parse($this->dateTo),
            $this->limit,
            $this->sortBy,
        );
    }

    public function render(): View
    {
        return view('livewire.reports.products');
    }
}
