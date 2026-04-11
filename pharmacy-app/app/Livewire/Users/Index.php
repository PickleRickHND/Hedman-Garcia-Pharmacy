<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Usuarios')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'role', history: true)]
    public string $roleFilter = '';

    #[Url(as: 'sort', history: true)]
    public string $sortField = 'name';

    #[Url(as: 'dir', history: true)]
    public string $sortDirection = 'asc';

    public ?string $flashMessage = null;
    public ?string $flashVariant = null;
    public ?string $generatedPassword = null;
    public ?int $generatedPasswordUserId = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function forceReset(int $userId): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            $this->flashVariant = 'danger';
            $this->flashMessage = 'No puedes resetear tu propia contraseña desde esta pantalla.';
            return;
        }

        $newPassword = Str::random(12);

        $user->forceFill([
            'password' => Hash::make($newPassword),
            'must_change_password' => true,
        ])->save();

        $this->generatedPassword = $newPassword;
        $this->generatedPasswordUserId = $user->id;
        $this->flashVariant = 'success';
        $this->flashMessage = "Contraseña temporal generada para {$user->name}. Compártesela en persona — solo se muestra una vez.";
    }

    public function deleteUser(int $userId): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        if ($userId === auth()->id()) {
            $this->flashVariant = 'danger';
            $this->flashMessage = 'No puedes eliminar tu propia cuenta.';
            return;
        }

        $user = User::findOrFail($userId);
        $name = $user->name;
        $user->delete();

        $this->flashVariant = 'success';
        $this->flashMessage = "Usuario «{$name}» eliminado.";
    }

    #[Computed]
    public function roles()
    {
        return Role::orderBy('name')->pluck('name');
    }

    public function render(): View
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when($this->roleFilter, fn ($q) => $q->role($this->roleFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
        ]);
    }
}
