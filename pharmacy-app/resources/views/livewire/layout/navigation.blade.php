<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
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

<nav x-data="{ open: false }" class="bg-white dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                @php
                    $salesActive = request()->routeIs('billing.*')
                        || request()->routeIs('returns.*')
                        || request()->routeIs('cash-register.*')
                        || request()->routeIs('customers.*');
                    $inventoryActive = request()->routeIs('inventory.*')
                        || request()->routeIs('products.*')
                        || request()->routeIs('categories.*')
                        || request()->routeIs('suppliers.*');
                    $adminActive = request()->routeIs('users.*') || request()->routeIs('reports.*');

                    $triggerBase = 'inline-flex h-full items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';
                    $triggerInactive = 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700';
                    $triggerActive = 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100';
                    $menuPanel = 'absolute left-0 top-full z-50 mt-0 w-48 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 bg-white dark:bg-gray-700 py-1';
                @endphp

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        Dashboard
                    </x-nav-link>

                    @hasanyrole('Administrador|Cajero')
                        <div class="relative inline-flex"
                             x-data="{ open: false, timer: null, show() { clearTimeout(this.timer); this.open = true; }, hide() { this.timer = setTimeout(() => this.open = false, 150); } }"
                             @mouseenter="show()" @mouseleave="hide()" @click.outside="open = false">
                            <button type="button" @click="open = !open" class="{{ $triggerBase }} {{ $salesActive ? $triggerActive : $triggerInactive }}">
                                Ventas
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                            <div x-show="open" x-cloak style="display:none;"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                                 class="{{ $menuPanel }}" @click="open = false">
                                <x-dropdown-link :href="route('billing.index')" wire:navigate>Facturación</x-dropdown-link>
                                <x-dropdown-link :href="route('returns.index')" wire:navigate>Devoluciones</x-dropdown-link>
                                <x-dropdown-link :href="route('cash-register.index')" wire:navigate>Caja</x-dropdown-link>
                                <x-dropdown-link :href="route('customers.index')" wire:navigate>Clientes</x-dropdown-link>
                            </div>
                        </div>

                        <div class="relative inline-flex"
                             x-data="{ open: false, timer: null, show() { clearTimeout(this.timer); this.open = true; }, hide() { this.timer = setTimeout(() => this.open = false, 150); } }"
                             @mouseenter="show()" @mouseleave="hide()" @click.outside="open = false">
                            <button type="button" @click="open = !open" class="{{ $triggerBase }} {{ $inventoryActive ? $triggerActive : $triggerInactive }}">
                                Inventario
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                            <div x-show="open" x-cloak style="display:none;"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                                 class="{{ $menuPanel }}" @click="open = false">
                                <x-dropdown-link :href="route('inventory.index')" wire:navigate>Productos</x-dropdown-link>
                                <x-dropdown-link :href="route('inventory.movements')" wire:navigate>Kardex</x-dropdown-link>
                                @role('Administrador')
                                    <x-dropdown-link :href="route('categories.index')" wire:navigate>Categorías</x-dropdown-link>
                                    <x-dropdown-link :href="route('suppliers.index')" wire:navigate>Proveedores</x-dropdown-link>
                                @endrole
                            </div>
                        </div>
                    @endhasanyrole

                    @role('Administrador')
                        <div class="relative inline-flex"
                             x-data="{ open: false, timer: null, show() { clearTimeout(this.timer); this.open = true; }, hide() { this.timer = setTimeout(() => this.open = false, 150); } }"
                             @mouseenter="show()" @mouseleave="hide()" @click.outside="open = false">
                            <button type="button" @click="open = !open" class="{{ $triggerBase }} {{ $adminActive ? $triggerActive : $triggerInactive }}">
                                Administración
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                            <div x-show="open" x-cloak style="display:none;"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                                 class="{{ $menuPanel }}" @click="open = false">
                                <x-dropdown-link :href="route('users.index')" wire:navigate>Usuarios</x-dropdown-link>
                                <x-dropdown-link :href="route('reports.index')" wire:navigate>Reportes</x-dropdown-link>
                            </div>
                        </div>
                    @endrole
                </div>
            </div>

            <!-- Notifications + Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:gap-2">
                @hasanyrole('Administrador|Cajero')
                    <livewire:notifications.bell />
                @endhasanyrole
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                Dashboard
            </x-responsive-nav-link>
            @role('Administrador')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire:navigate>
                    Usuarios
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" wire:navigate>
                    Categorías
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" wire:navigate>
                    Proveedores
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" wire:navigate>
                    Reportes
                </x-responsive-nav-link>
            @endrole
            @hasanyrole('Administrador|Cajero')
                <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate>
                    Clientes
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.index') || request()->routeIs('products.*')" wire:navigate>
                    Inventario
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('inventory.movements')" :active="request()->routeIs('inventory.movements')" wire:navigate>
                    Kardex
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('billing.index')" :active="request()->routeIs('billing.*')" wire:navigate>
                    Facturación
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('returns.index')" :active="request()->routeIs('returns.*')" wire:navigate>
                    Devoluciones
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cash-register.index')" :active="request()->routeIs('cash-register.*')" wire:navigate>
                    Caja
                </x-responsive-nav-link>
            @endhasanyrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
