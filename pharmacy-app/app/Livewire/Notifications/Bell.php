<?php

declare(strict_types=1);

namespace App\Livewire\Notifications;

use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Bell extends Component
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    #[Computed]
    public function alerts(): array
    {
        return app(NotificationService::class)->getAlerts();
    }

    #[Computed]
    public function totalCount(): int
    {
        return app(NotificationService::class)->getTotalCount();
    }

    public function render(): View
    {
        return view('livewire.notifications.bell');
    }
}
