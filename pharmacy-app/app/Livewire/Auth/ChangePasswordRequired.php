<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Cambiar contraseña')]
class ChangePasswordRequired extends Component
{
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        // Si no es requerido, redirigir al dashboard
        if (! auth()->user()?->must_change_password) {
            $this->redirectRoute('dashboard', navigate: false);
        }
    }

    public function save()
    {
        $this->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->forceFill([
            'password' => Hash::make($this->password),
            'must_change_password' => false,
        ])->save();

        session()->flash('status', 'Contraseña actualizada. Bienvenido.');

        return redirect()->route('dashboard');
    }

    public function render(): View
    {
        return view('livewire.auth.change-password-required');
    }
}
