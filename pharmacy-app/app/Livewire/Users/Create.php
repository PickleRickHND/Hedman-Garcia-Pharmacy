<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Nuevo usuario')]
class Create extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = '';
    public bool $must_change_password = true;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:'.config('pharmacy.limits.user_name_max')],
            'email' => ['required', 'email', 'max:'.config('pharmacy.limits.user_email_max'), Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(Role::pluck('name')->toArray())],
            'must_change_password' => ['boolean'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'must_change_password' => $validated['must_change_password'],
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$validated['role']]);

        session()->flash('users.flash', 'Usuario «'.$user->name.'» creado correctamente.');

        return redirect()->route('users.index');
    }

    public function render(): View
    {
        return view('livewire.users.create', [
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
