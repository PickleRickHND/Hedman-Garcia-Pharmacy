<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        // nosemgrep: php.symfony.security.audit.symfony-non-literal-redirect.symfony-non-literal-redirect
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<x-auth-shell
    step="02"
    eyebrow="Crear cuenta"
    title="Únete al sistema."
    description="Crea tu cuenta para comenzar a operar."
    headline="Una plataforma"
    headline-italic="hecha para tu equipo."
    subline="Gestión colaborativa con roles, trazabilidad y reportes al instante. Diseñada para equipos que valoran la precisión."
>
    <form wire:submit="register" class="space-y-6" novalidate>

        <div>
            <label for="name" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wider text-surface-600 dark:text-surface-400">
                Nombre completo
            </label>
            <input
                wire:model="name"
                id="name"
                type="text"
                name="name"
                required
                autofocus
                autocomplete="name"
                placeholder="Tu nombre y apellido"
                class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
            />
            @error('name')<p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>@enderror
        </div>

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
                autocomplete="username"
                placeholder="tu@pharmacy.hn"
                class="block w-full border-0 border-b border-surface-300 bg-transparent px-0 py-2.5 text-base text-surface-900 placeholder:text-surface-400 focus:border-brand-600 focus:outline-none focus:ring-0 dark:border-surface-700 dark:text-surface-50 dark:placeholder:text-surface-600 dark:focus:border-brand-500"
            />
            @error('email')<p class="mt-1.5 text-xs text-danger" role="alert">{{ $message }}</p>@enderror
        </div>

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
                <span wire:loading.remove wire:target="register">Crear cuenta</span>
                <span wire:loading wire:target="register">Registrando…</span>
                <svg wire:loading.remove wire:target="register" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>
        </div>
    </form>

    <p class="mt-10 border-t border-surface-200 pt-6 text-center text-xs text-surface-500 dark:border-surface-800 dark:text-surface-500">
        ¿Ya tienes una cuenta?
        <a
            href="{{ route('login') }}"
            wire:navigate
            class="font-medium text-surface-900 underline-offset-4 transition-colors hover:text-brand-700 hover:underline focus-visible:text-brand-700 focus-visible:underline focus-visible:outline-none dark:text-surface-100 dark:hover:text-brand-400"
        >
            Inicia sesión
        </a>
    </p>
</x-auth-shell>
