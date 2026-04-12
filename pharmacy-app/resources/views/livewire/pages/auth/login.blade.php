<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // nosemgrep: php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex min-h-screen flex-col md:flex-row">

    {{-- Panel visual --}}
    <aside
        class="relative flex flex-col justify-between overflow-hidden bg-brand-900 px-8 py-10 text-brand-50 md:w-1/2 md:px-12 md:py-14 lg:w-[55%] lg:px-20 lg:py-20"
        aria-hidden="true"
    >
        <div class="absolute left-0 right-0 top-0 h-px bg-brand-700/60"></div>

        <div
            class="pointer-events-none absolute inset-0 opacity-[0.035]"
            style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 48px 48px;"
        ></div>

        <div class="relative animate-fade-in">
            <div class="flex items-start justify-between gap-6">
                <div class="flex items-center gap-3">
                    <svg viewBox="0 0 48 48" class="h-11 w-11 shrink-0" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Logo Hedman Garcia Pharmacy">
                        <g transform="rotate(-45 24 24)">
                            <rect x="6" y="18" width="36" height="12" rx="6" stroke="currentColor" stroke-width="1.5"/>
                            <line x1="24" y1="18" x2="24" y2="30" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="6" y="18" width="18" height="12" rx="6" fill="currentColor" fill-opacity="0.12"/>
                        </g>
                    </svg>
                    <div class="flex flex-col leading-tight">
                        <span class="font-['Fraunces'] text-lg font-medium tracking-tight text-brand-50">Hedman Garcia</span>
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em] text-brand-300">Pharmacy</span>
                    </div>
                </div>

                <span class="hidden shrink-0 font-mono text-[10px] uppercase tracking-[0.2em] text-brand-300 md:inline">
                    01 — Acceso
                </span>
            </div>
        </div>

        <div class="relative mt-10 max-w-xl animate-fade-in md:mt-0">
            <p class="mb-6 font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400">
                Sistema de gestión · v2
            </p>
            <h1 class="font-['Fraunces'] text-4xl font-[450] leading-[1.05] tracking-tight text-brand-50 md:text-5xl lg:text-[3.5rem]">
                Gestión farmacéutica<br>
                <em class="font-[450] italic text-brand-200">diseñada con precisión.</em>
            </h1>
            <p class="mt-6 max-w-md text-sm leading-relaxed text-brand-200/80 md:text-base">
                Control de inventario, facturación transaccional y bitácora clínica
                en una sola plataforma. Auditable, segura, y con el rigor que una
                farmacia moderna necesita.
            </p>
        </div>

        <div class="relative mt-10 flex items-end justify-between gap-4 border-t border-brand-700/60 pt-6 font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 md:mt-0">
            <span>MMXXVI · Tegucigalpa, HN</span>
            <span class="hidden sm:inline">SSL · bcrypt · RBAC</span>
        </div>
    </aside>

    {{-- Panel formulario --}}
    <section class="flex flex-1 items-center justify-center bg-surface-50 px-6 py-12 dark:bg-surface-950 sm:px-10 md:w-1/2 lg:w-[45%] lg:px-16">
        <div class="w-full max-w-sm animate-fade-in">

            <header class="mb-10">
                <p class="mb-3 font-mono text-[10px] uppercase tracking-[0.2em] text-surface-400 dark:text-surface-500">
                    Iniciar sesión
                </p>
                <h2 class="font-['Fraunces'] text-3xl font-[500] tracking-tight text-surface-900 dark:text-surface-50">
                    Bienvenido de vuelta.
                </h2>
                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                    Ingresa tus credenciales para continuar.
                </p>
            </header>

            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form wire:submit="login" class="space-y-6" novalidate>
                <div>
                    <label for="email" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wider text-surface-600 dark:text-surface-400">
                        Correo electrónico
                    </label>
                    <input
                        wire:model="form.email"
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="tu@pharmacy.hn"
                        class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
                    />
                    @error('form.email')
                        <p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wider text-surface-600 dark:text-surface-400">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input
                            wire:model="form.password"
                            :type="show ? 'text' : 'password'"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 pr-10 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
                        />
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute right-0 top-1/2 -translate-y-1/2 p-1 text-surface-400 transition-colors hover:text-surface-600 focus-visible:text-brand-600 focus-visible:outline-none dark:hover:text-surface-300 dark:focus-visible:text-brand-400"
                            :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                        >
                            <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('form.password')
                        <p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label for="remember" class="group flex cursor-pointer items-center gap-2 text-xs text-surface-600 dark:text-surface-400">
                        <input
                            wire:model="form.remember"
                            id="remember"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded-sm border-surface-300 text-brand-600 focus:ring-brand-500 focus:ring-offset-0 dark:border-surface-600 dark:bg-surface-900"
                        />
                        <span class="transition-colors group-hover:text-surface-900 dark:group-hover:text-surface-100">
                            Mantener sesión iniciada
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            wire:navigate
                            class="text-xs font-medium text-surface-600 underline-offset-4 transition-colors hover:text-brand-700 hover:underline focus-visible:text-brand-700 focus-visible:underline focus-visible:outline-none dark:text-surface-400 dark:hover:text-brand-400"
                        >
                            ¿La olvidaste?
                        </a>
                    @endif
                </div>

                <div class="pt-4">
                    <button
                        type="submit"
                        class="group relative flex w-full items-center justify-center gap-2 rounded-none bg-surface-900 px-6 py-3.5 text-sm font-medium tracking-wide text-surface-50 transition-all duration-200 hover:bg-brand-700 focus-visible:bg-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 focus-visible:ring-offset-surface-50 disabled:opacity-60 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400 dark:focus-visible:ring-offset-surface-950"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="login" class="flex items-center gap-2">
                            Iniciar sesión
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </span>
                        <span wire:loading wire:target="login" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                            Verificando…
                        </span>
                    </button>
                </div>
            </form>

            @if (Route::has('register'))
                <p class="mt-10 border-t border-surface-200 pt-6 text-center text-xs text-surface-500 dark:border-surface-800 dark:text-surface-500">
                    ¿No tienes cuenta?
                    <a
                        href="{{ route('register') }}"
                        wire:navigate
                        class="font-medium text-surface-900 underline-offset-4 transition-colors hover:text-brand-700 hover:underline focus-visible:text-brand-700 focus-visible:underline focus-visible:outline-none dark:text-surface-100 dark:hover:text-brand-400"
                    >
                        Regístrate aquí
                    </a>
                </p>
            @endif

            <p class="mt-8 text-center font-mono text-[9px] uppercase tracking-[0.2em] text-surface-400 md:hidden dark:text-surface-600">
                MMXXVI · Tegucigalpa, HN
            </p>
        </div>
    </section>
</div>
