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
#[Title('Editar usuario')]
class Edit extends Component
{
    public User $user;
    public string $name = '';
    public string $email = '';
    public string $role = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(User $user): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->primary_role ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:'.config('pharmacy.limits.user_name_max')],
            'email' => ['required', 'email', 'max:'.config('pharmacy.limits.user_email_max'), Rule::unique('users', 'email')->ignore($this->user->id)],
            'role' => ['required', Rule::in(Role::pluck('name')->toArray())],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $updates = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updates['password'] = Hash::make($validated['password']);
            $updates['must_change_password'] = false;
        }

        $this->user->update($updates);
        $this->user->syncRoles([$validated['role']]);

        session()->flash('users.flash', 'Usuario actualizado correctamente.');

        return redirect()->route('users.index');
    }

    public function render(): View
    {
        return view('livewire.users.edit', [
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
