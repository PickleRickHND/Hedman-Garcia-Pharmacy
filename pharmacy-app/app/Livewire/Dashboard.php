<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'metrics' => [
                'users_total' => User::count(),
                'users_admins' => User::role('Administrador')->count(),
                'users_cashiers' => User::role('Cajero')->count(),
                'products_total' => 0, // placeholder Fase 3
                'low_stock' => 0,      // placeholder Fase 3
                'expiring_soon' => 0,  // placeholder Fase 3
                'invoices_today' => 0, // placeholder Fase 4
                'revenue_today' => 0,  // placeholder Fase 4
            ],
        ]);
    }
}
