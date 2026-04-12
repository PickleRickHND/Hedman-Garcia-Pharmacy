<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<x-auth-shell
    step="03"
    eyebrow="Recuperar acceso"
    title="¿Olvidaste tu contraseña?"
    description="Te enviaremos un enlace para reestablecerla."
    headline="Todo el mundo"
    headline-italic="olvida una contraseña."
    subline="Sin drama. Te enviamos un enlace seguro por correo y vuelves al trabajo en minutos."
>
    <div class="mb-6 text-sm text-surface-600 dark:text-surface-400">
        Escribe el correo asociado a tu cuenta y te enviaremos instrucciones
        para establecer una nueva contraseña.
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-6" novalidate>
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
                placeholder="tu@pharmacy.hn"
                class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
            />
            @error('email')<p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="pt-4">
            <button
                type="submit"
                class="group flex w-full items-center justify-center gap-2 bg-surface-900 px-6 py-3.5 text-sm font-medium tracking-wide text-surface-50 transition-all duration-200 hover:bg-brand-700 focus-visible:bg-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 focus-visible:ring-offset-surface-50 disabled:opacity-60 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400 dark:focus-visible:ring-offset-surface-950"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="sendPasswordResetLink">Enviar enlace</span>
                <span wire:loading wire:target="sendPasswordResetLink">Enviando…</span>
            </button>
        </div>
    </form>

    <p class="mt-10 border-t border-surface-200 pt-6 text-center text-xs text-surface-500 dark:border-surface-800 dark:text-surface-500">
        <a
            href="{{ route('login') }}"
            wire:navigate
            class="font-medium text-surface-900 underline-offset-4 transition-colors hover:text-brand-700 hover:underline focus-visible:text-brand-700 focus-visible:underline focus-visible:outline-none dark:text-surface-100 dark:hover:text-brand-400"
        >
            ← Volver al inicio de sesión
        </a>
    </p>
</x-auth-shell>
