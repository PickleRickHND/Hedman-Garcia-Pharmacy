<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Product;
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
                'products_total' => Product::count(),
                'low_stock' => Product::lowStock()->count(),
                'expiring_soon' => Product::expiringSoon()->count(),
                'invoices_today' => Invoice::emitted()->forToday()->count(),
                'revenue_today' => (float) Invoice::emitted()->forToday()->sum('total'),
            ],
        ]);
    }
}
