<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        // nosemgrep: php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<x-auth-shell
    step="06"
    eyebrow="Área segura"
    title="Confirma tu contraseña."
    description="Esta es un área sensible del sistema."
    headline="Seguridad"
    headline-italic="en cada capa."
    subline="Antes de continuar, necesitamos confirmar que eres tú. Introduce tu contraseña actual para proceder."
>
    <form wire:submit="confirmPassword" class="space-y-6" novalidate>
        <div>
            <label for="password" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wider text-surface-600 dark:text-surface-400">
                Contraseña
            </label>
            <input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="current-password"
                placeholder="••••••••"
                class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
            />
            @error('password')<p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="pt-4">
            <button
                type="submit"
                class="group flex w-full items-center justify-center gap-2 bg-surface-900 px-6 py-3.5 text-sm font-medium tracking-wide text-surface-50 transition-all duration-200 hover:bg-brand-700 focus-visible:bg-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 focus-visible:ring-offset-surface-50 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400 dark:focus-visible:ring-offset-surface-950"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="confirmPassword">Confirmar</span>
                <span wire:loading wire:target="confirmPassword">Verificando…</span>
            </button>
        </div>
    </form>
</x-auth-shell>
