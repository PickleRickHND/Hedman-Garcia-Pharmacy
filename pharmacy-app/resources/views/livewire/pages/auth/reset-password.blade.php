<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true); // nosemgrep: php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
    }
}; ?>

<x-auth-shell
    step="04"
    eyebrow="Nueva contraseña"
    title="Restablece tu acceso."
    description="Elige una contraseña nueva y vuelve a la plataforma."
    headline="Una contraseña"
    headline-italic="fuerte, memorable."
    subline="Mínimo 8 caracteres. Combina letras, números y un símbolo. Nosotros guardamos solo el hash."
>
    <form wire:submit="resetPassword" class="space-y-6" novalidate>

        <div>
            <label for="email" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wider text-surface-600 dark:text-surface-400">
                Correo electrónico
            </label>
            <input
                wire:model="email"
                id="email"
                type="email"
                name="email"
                required
                autofocus
                autocomplete="username"
                class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
            />
            @error('email')<p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wider text-surface-600 dark:text-surface-400">
                Nueva contraseña
            </label>
            <input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
            />
            @error('password')<p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wider text-surface-600 dark:text-surface-400">
                Confirmar contraseña
            </label>
            <input
                wire:model="password_confirmation"
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
            />
            @error('password_confirmation')<p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="pt-4">
            <button
                type="submit"
                class="group flex w-full items-center justify-center gap-2 bg-surface-900 px-6 py-3.5 text-sm font-medium tracking-wide text-surface-50 transition-all duration-200 hover:bg-brand-700 focus-visible:bg-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 focus-visible:ring-offset-surface-50 disabled:opacity-60 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400 dark:focus-visible:ring-offset-surface-950"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="resetPassword">Restablecer contraseña</span>
                <span wire:loading wire:target="resetPassword">Actualizando…</span>
            </button>
        </div>
    </form>
</x-auth-shell>
