<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Hedman Garcia Pharmacy') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,700,900|inter:400,500,600,700&display=swap" rel="stylesheet" />

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased bg-surface-50 text-surface-900 dark:bg-surface-950 dark:text-surface-100 selection:bg-brand-200 selection:text-brand-900 dark:selection:bg-brand-800 dark:selection:text-brand-50">

        <div class="relative flex min-h-screen flex-col">

            <header class="relative z-10">
                <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-10">
                    <a href="/" class="group flex items-center gap-3" aria-label="Inicio">
                        <svg viewBox="0 0 48 48" class="h-9 w-9 text-brand-700 transition-transform duration-300 group-hover:rotate-[-12deg] dark:text-brand-400" fill="none">
                            <g transform="rotate(-45 24 24)">
                                <rect x="6" y="18" width="36" height="12" rx="6" stroke="currentColor" stroke-width="1.5"/>
                                <line x1="24" y1="18" x2="24" y2="30" stroke="currentColor" stroke-width="1.5"/>
                                <rect x="6" y="18" width="18" height="12" rx="6" fill="currentColor" fill-opacity="0.15"/>
                            </g>
                        </svg>
                        <div class="flex flex-col leading-tight">
                            <span class="font-['Fraunces'] text-base font-medium tracking-tight text-surface-900 dark:text-surface-50">Hedman Garcia</span>
                            <span class="text-[10px] font-medium uppercase tracking-[0.18em] text-surface-500 dark:text-surface-400">Pharmacy</span>
                        </div>
                    </a>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-4 text-sm">
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="font-medium text-surface-900 underline-offset-4 transition-colors hover:text-brand-700 hover:underline dark:text-surface-100 dark:hover:text-brand-400"
                                >
                                    Ir al dashboard →
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="text-sm font-medium text-surface-600 underline-offset-4 transition-colors hover:text-brand-700 hover:underline dark:text-surface-400 dark:hover:text-brand-400"
                                >
                                    Iniciar sesión
                                </a>
                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="inline-flex items-center gap-1.5 bg-surface-900 px-4 py-2 text-xs font-medium tracking-wide text-surface-50 transition-colors hover:bg-brand-700 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400"
                                    >
                                        Crear cuenta
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </nav>
            </header>

            <section class="relative flex-1 overflow-hidden">
                <div
                    class="pointer-events-none absolute inset-0 opacity-[0.04] dark:opacity-[0.07]"
                    style="background-image: linear-gradient(to right, currentColor 1px, transparent 1px), linear-gradient(to bottom, currentColor 1px, transparent 1px); background-size: 64px 64px;"
                ></div>

                <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-12 lg:gap-16 lg:px-10 lg:py-24">

                    <div class="lg:col-span-7 animate-fade-in">
                        <p class="mb-6 font-mono text-[10px] uppercase tracking-[0.25em] text-surface-500 dark:text-surface-400">
                            <span class="inline-block h-1 w-8 bg-brand-600 align-middle"></span>
                            Sistema de gestión farmacéutica · MMXXVI
                        </p>
                        <h1 class="font-['Fraunces'] text-5xl font-[450] leading-[0.95] tracking-tight text-surface-900 sm:text-6xl lg:text-[5.5rem] dark:text-surface-50">
                            Una farmacia,<br>
                            <em class="font-[450] italic text-brand-700 dark:text-brand-400">bien medida.</em>
                        </h1>
                        <p class="mt-8 max-w-xl text-base leading-relaxed text-surface-600 sm:text-lg dark:text-surface-400">
                            Gestión de inventario, facturación transaccional y control de usuarios
                            con el rigor y la claridad que tu farmacia merece. Construida en Laravel,
                            diseñada para operar.
                        </p>

                        <div class="mt-10 flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="group inline-flex items-center gap-2 bg-surface-900 px-6 py-3.5 text-sm font-medium tracking-wide text-surface-50 transition-all duration-200 hover:bg-brand-700 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400"
                                >
                                    Ir al dashboard
                                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            @else
                                @if (Route::has('login'))
                                    <a
                                        href="{{ route('login') }}"
                                        class="group inline-flex items-center gap-2 bg-surface-900 px-6 py-3.5 text-sm font-medium tracking-wide text-surface-50 transition-all duration-200 hover:bg-brand-700 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400"
                                    >
                                        Iniciar sesión
                                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                @endif
                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="text-sm font-medium text-surface-600 underline-offset-4 transition-colors hover:text-brand-700 hover:underline dark:text-surface-400 dark:hover:text-brand-400"
                                    >
                                        O crea una cuenta gratis →
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="relative lg:col-span-5 animate-fade-in">
                        <div class="relative aspect-[4/5] w-full overflow-hidden bg-brand-900 p-8 text-brand-50 sm:p-10">
                            <div
                                class="pointer-events-none absolute inset-0 opacity-[0.04]"
                                style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 32px 32px;"
                            ></div>
                            <div class="absolute left-0 right-0 top-0 h-px bg-brand-700/60"></div>

                            <svg viewBox="0 0 200 200" class="absolute -right-8 -top-8 h-[22rem] w-[22rem] text-brand-800" fill="none">
                                <g transform="rotate(-45 100 100)">
                                    <rect x="20" y="70" width="160" height="60" rx="30" stroke="currentColor" stroke-width="1"/>
                                    <line x1="100" y1="70" x2="100" y2="130" stroke="currentColor" stroke-width="1"/>
                                    <rect x="20" y="70" width="80" height="60" rx="30" fill="currentColor" fill-opacity="0.4"/>
                                </g>
                            </svg>

                            <div class="relative flex h-full flex-col justify-between">
                                <div>
                                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-300">— Ficha técnica</p>
                                </div>

                                <div class="space-y-4">
                                    <dl class="space-y-3 border-t border-brand-700/60 pt-4 text-sm">
                                        <div class="flex justify-between">
                                            <dt class="font-mono text-[10px] uppercase tracking-[0.15em] text-brand-300">Stack</dt>
                                            <dd class="text-brand-50">Laravel 11 · Livewire 3</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="font-mono text-[10px] uppercase tracking-[0.15em] text-brand-300">Frontend</dt>
                                            <dd class="text-brand-50">Tailwind · Alpine</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="font-mono text-[10px] uppercase tracking-[0.15em] text-brand-300">DB</dt>
                                            <dd class="text-brand-50">MySQL 8</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="font-mono text-[10px] uppercase tracking-[0.15em] text-brand-300">Auth</dt>
                                            <dd class="text-brand-50">Spatie · bcrypt · RBAC</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative border-t border-surface-200 dark:border-surface-800">
                    <div class="mx-auto grid max-w-7xl grid-cols-1 divide-y divide-surface-200 px-6 md:grid-cols-3 md:divide-x md:divide-y-0 lg:px-10 dark:divide-surface-800">
                        <div class="flex items-start gap-4 py-8 pr-6 md:py-10">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">01</span>
                            <div>
                                <h3 class="font-['Fraunces'] text-lg font-medium text-surface-900 dark:text-surface-50">Inventario vivo</h3>
                                <p class="mt-2 text-sm leading-relaxed text-surface-600 dark:text-surface-400">
                                    Búsqueda en tiempo real, alertas de stock bajo y vencimientos próximos.
                                    Soft deletes para preservar historial.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 py-8 md:px-6 md:py-10">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">02</span>
                            <div>
                                <h3 class="font-['Fraunces'] text-lg font-medium text-surface-900 dark:text-surface-50">Facturación transaccional</h3>
                                <p class="mt-2 text-sm leading-relaxed text-surface-600 dark:text-surface-400">
                                    Punto de venta con cálculo automático de ISV, descuento de stock
                                    atómico y descarga de PDF.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 py-8 pl-6 md:py-10">
                            <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">03</span>
                            <div>
                                <h3 class="font-['Fraunces'] text-lg font-medium text-surface-900 dark:text-surface-50">Control por roles</h3>
                                <p class="mt-2 text-sm leading-relaxed text-surface-600 dark:text-surface-400">
                                    Administrador, Cajero e Invitado. Permisos granulares con
                                    Spatie Permission y middleware por ruta.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="relative border-t border-surface-200 dark:border-surface-800">
                <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-4 px-6 py-8 sm:flex-row sm:items-center lg:px-10">
                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-surface-500 dark:text-surface-400">
                        MMXXVI · Tegucigalpa, Honduras
                    </p>
                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-surface-500 dark:text-surface-400">
                        Hedman Garcia Pharmacy
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>
