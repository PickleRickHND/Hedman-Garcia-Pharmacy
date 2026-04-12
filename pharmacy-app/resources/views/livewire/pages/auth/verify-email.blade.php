<?php

use App\Livewire\Actions\Logout;
use Illuminate\Http\RedirectResponse;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            // nosemgrep: php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        auth()->user()->sendEmailVerificationNotification();

        session()->flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        // nosemgrep: php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
        $this->redirect('/', navigate: true);
    }
}; ?>

<x-auth-shell
    step="05"
    eyebrow="Verificar correo"
    title="Confirma tu email."
    description="Te enviamos un enlace para verificar tu cuenta."
    headline="Un paso más"
    headline-italic="para estar listos."
    subline="Necesitamos verificar tu correo electrónico. Hemos enviado un enlace a la dirección con la que te registraste."
>
    <div class="mb-6 text-sm text-surface-600 dark:text-surface-400">
        Gracias por registrarte. Antes de comenzar, ¿podrías verificar tu dirección
        de correo haciendo clic en el enlace que acabamos de enviarte? Si no lo
        recibiste, con gusto te enviaremos otro.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 border border-success/30 bg-success/10 px-4 py-3 text-sm text-success" role="status">
            Un nuevo enlace de verificación fue enviado al correo que proporcionaste durante el registro.
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <button
            wire:click="sendVerification"
            class="group flex w-full items-center justify-center gap-2 bg-surface-900 px-6 py-3.5 text-sm font-medium tracking-wide text-surface-50 transition-all duration-200 hover:bg-brand-700 focus-visible:bg-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 focus-visible:ring-offset-surface-50 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400 dark:focus-visible:ring-offset-surface-950"
        >
            Reenviar verificación
        </button>

        <button
            wire:click="logout"
            class="text-center text-xs text-surface-500 underline-offset-4 transition-colors hover:text-surface-900 hover:underline focus-visible:text-surface-900 focus-visible:underline focus-visible:outline-none dark:text-surface-400 dark:hover:text-surface-100"
        >
            Cerrar sesión
        </button>
    </div>
</x-auth-shell>
