<div>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-surface-400 dark:text-surface-500">
                    <span class="inline-block h-1 w-6 bg-brand-600 align-middle"></span>
                    Panel operativo · {{ now()->format('d M Y') }}
                </p>
                <h1 class="mt-3 font-['Fraunces'] text-3xl font-[450] tracking-tight text-surface-900 dark:text-surface-50 sm:text-4xl">
                    Hola, {{ Str::before(auth()->user()->name, ' ') }}.
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 border border-brand-300 bg-brand-50 px-3 py-1.5 text-[11px] font-medium uppercase tracking-wider text-brand-800 dark:border-brand-800 dark:bg-brand-950/50 dark:text-brand-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                    {{ auth()->user()->primary_role ?? 'Sin rol' }}
                </span>
                <a href="{{ route('billing.create') }}" class="group hidden items-center gap-1.5 bg-surface-900 px-4 py-2 text-xs font-medium tracking-wide text-surface-50 transition-colors hover:bg-brand-700 sm:inline-flex dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400">
                    Nueva factura
                    <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- ============================================================
                 01 · PERSONAL
                 ============================================================ --}}
            <section class="animate-fade-in">
                <div class="mb-5 flex items-baseline gap-3">
                    <span class="font-['Fraunces'] text-5xl font-[400] italic text-surface-200 dark:text-surface-800">01</span>
                    <div>
                        <h2 class="font-['Fraunces'] text-lg font-[500] text-surface-900 dark:text-surface-50">Personal</h2>
                        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-surface-400 dark:text-surface-600">Equipo activo</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-px overflow-hidden rounded-lg border border-surface-200 bg-surface-200 sm:grid-cols-3 dark:border-surface-800 dark:bg-surface-800">
                    {{-- Total --}}
                    <div class="flex items-center justify-between bg-white p-6 dark:bg-surface-900">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Total</p>
                            <p class="mt-1 font-['Fraunces'] text-4xl font-[500] tracking-tight text-surface-900 dark:text-surface-50">{{ $metrics['users_total'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-950/50 dark:text-brand-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        </div>
                    </div>
                    {{-- Admins --}}
                    <div class="flex items-center justify-between bg-white p-6 dark:bg-surface-900">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Admins</p>
                            <p class="mt-1 font-['Fraunces'] text-4xl font-[500] tracking-tight text-surface-900 dark:text-surface-50">{{ $metrics['users_admins'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        </div>
                    </div>
                    {{-- Cajeros --}}
                    <div class="flex items-center justify-between bg-white p-6 dark:bg-surface-900">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Cajeros</p>
                            <p class="mt-1 font-['Fraunces'] text-4xl font-[500] tracking-tight text-surface-900 dark:text-surface-50">{{ $metrics['users_cashiers'] }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </div>
                    </div>
                </div>
            </section>


            {{-- ============================================================
                 02 · INVENTARIO
                 ============================================================ --}}
            <section class="mt-12 animate-fade-in sm:mt-16">
                <div class="mb-5 flex items-baseline gap-3">
                    <span class="font-['Fraunces'] text-5xl font-[400] italic text-surface-200 dark:text-surface-800">02</span>
                    <div>
                        <h2 class="font-['Fraunces'] text-lg font-[500] text-surface-900 dark:text-surface-50">Inventario</h2>
                        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-surface-400 dark:text-surface-600">Catálogo farmacéutico</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    {{-- Productos totales --}}
                    <div class="group relative overflow-hidden rounded-lg border border-surface-200 bg-white p-6 transition-shadow duration-200 hover:shadow-card-hover dark:border-surface-800 dark:bg-surface-900">
                        <div class="absolute -right-4 -top-4 font-['Fraunces'] text-[5rem] font-[400] leading-none text-surface-100 dark:text-surface-800/50">Rx</div>
                        <div class="relative">
                            <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Productos</p>
                            <p class="mt-2 font-['Fraunces'] text-5xl font-[500] tracking-tight text-surface-900 dark:text-surface-50">{{ $metrics['products_total'] }}</p>
                            <p class="mt-3 text-xs text-surface-500 dark:text-surface-400">En catálogo activo</p>
                        </div>
                    </div>

                    {{-- Stock bajo --}}
                    <div class="group relative overflow-hidden rounded-lg border {{ $metrics['low_stock'] > 0 ? 'border-amber-200 bg-amber-50/50 dark:border-amber-900/50 dark:bg-amber-950/20' : 'border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900' }} p-6 transition-shadow duration-200 hover:shadow-card-hover">
                        <div class="relative">
                            <div class="flex items-center gap-2">
                                <p class="font-mono text-[10px] uppercase tracking-[0.15em] {{ $metrics['low_stock'] > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-surface-400 dark:text-surface-500' }}">Stock bajo</p>
                                @if ($metrics['low_stock'] > 0)
                                    <span class="inline-flex h-2 w-2 rounded-full bg-amber-500 animate-pulse" aria-label="Requiere atención"></span>
                                @endif
                            </div>
                            <p class="mt-2 font-['Fraunces'] text-5xl font-[500] tracking-tight {{ $metrics['low_stock'] > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-surface-300 dark:text-surface-700' }}">
                                {{ $metrics['low_stock'] > 0 ? $metrics['low_stock'] : '—' }}
                            </p>
                            <p class="mt-3 text-xs {{ $metrics['low_stock'] > 0 ? 'text-amber-600/80 dark:text-amber-400/80' : 'text-surface-400 dark:text-surface-600' }}">
                                {{ $metrics['low_stock'] > 0 ? 'Requieren reposición' : 'Todo en orden' }}
                            </p>
                        </div>
                    </div>

                    {{-- Próximos a vencer --}}
                    <div class="group relative overflow-hidden rounded-lg border {{ $metrics['expiring_soon'] > 0 ? 'border-red-200 bg-red-50/50 dark:border-red-900/50 dark:bg-red-950/20' : 'border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900' }} p-6 transition-shadow duration-200 hover:shadow-card-hover">
                        <div class="relative">
                            <div class="flex items-center gap-2">
                                <p class="font-mono text-[10px] uppercase tracking-[0.15em] {{ $metrics['expiring_soon'] > 0 ? 'text-red-700 dark:text-red-400' : 'text-surface-400 dark:text-surface-500' }}">Próximos a vencer</p>
                                @if ($metrics['expiring_soon'] > 0)
                                    <span class="inline-flex h-2 w-2 rounded-full bg-red-500 animate-pulse" aria-label="Requiere atención"></span>
                                @endif
                            </div>
                            <p class="mt-2 font-['Fraunces'] text-5xl font-[500] tracking-tight {{ $metrics['expiring_soon'] > 0 ? 'text-red-700 dark:text-red-300' : 'text-surface-300 dark:text-surface-700' }}">
                                {{ $metrics['expiring_soon'] > 0 ? $metrics['expiring_soon'] : '—' }}
                            </p>
                            <p class="mt-3 text-xs {{ $metrics['expiring_soon'] > 0 ? 'text-red-600/80 dark:text-red-400/80' : 'text-surface-400 dark:text-surface-600' }}">
                                {{ $metrics['expiring_soon'] > 0 ? 'En los próximos 30 días' : 'Sin urgencias' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>


            {{-- ============================================================
                 03 · FACTURACIÓN
                 ============================================================ --}}
            <section class="mt-12 animate-fade-in sm:mt-16">
                <div class="mb-5 flex items-baseline gap-3">
                    <span class="font-['Fraunces'] text-5xl font-[400] italic text-surface-200 dark:text-surface-800">03</span>
                    <div>
                        <h2 class="font-['Fraunces'] text-lg font-[500] text-surface-900 dark:text-surface-50">Facturación</h2>
                        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-surface-400 dark:text-surface-600">Operaciones del día</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Facturas hoy --}}
                    <div class="relative overflow-hidden rounded-lg border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-surface-400 dark:text-surface-500">Facturas hoy</p>
                                <p class="mt-2 font-['Fraunces'] text-5xl font-[500] tracking-tight {{ $metrics['invoices_today'] > 0 ? 'text-surface-900 dark:text-surface-50' : 'text-surface-300 dark:text-surface-700' }}">
                                    {{ $metrics['invoices_today'] > 0 ? $metrics['invoices_today'] : '—' }}
                                </p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-surface-500 dark:text-surface-400">{{ $metrics['invoices_today'] > 0 ? 'Emitidas desde las 00:00' : 'Aún sin operaciones' }}</p>
                    </div>

                    {{-- Ingresos --}}
                    <div class="relative overflow-hidden rounded-lg border border-brand-200 bg-brand-50/30 p-6 dark:border-brand-900/50 dark:bg-brand-950/20">
                        <div class="absolute -bottom-3 -right-3 font-['Fraunces'] text-[4rem] font-[400] italic leading-none text-brand-100 dark:text-brand-900/40">L.</div>
                        <div class="relative flex items-start justify-between">
                            <div>
                                <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-brand-700 dark:text-brand-400">Ingresos del día</p>
                                <p class="mt-2 font-['Fraunces'] text-4xl font-[500] tracking-tight text-brand-800 sm:text-5xl dark:text-brand-200">
                                    {{ $metrics['revenue_today'] > 0 ? 'L. ' . number_format($metrics['revenue_today'], 2) : '—' }}
                                </p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/50 dark:text-brand-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                            </div>
                        </div>
                        <p class="relative mt-3 text-xs text-brand-600/80 dark:text-brand-400/80">
                            {{ $metrics['revenue_today'] > 0 ? 'Acumulado del día' : 'Sin ingresos registrados' }}
                        </p>
                    </div>
                </div>
            </section>


            {{-- ============================================================
                 04 · ACCESOS RÁPIDOS
                 ============================================================ --}}
            <section class="mt-12 animate-fade-in sm:mt-16">
                <div class="mb-5 flex items-baseline gap-3">
                    <span class="font-['Fraunces'] text-5xl font-[400] italic text-surface-200 dark:text-surface-800">04</span>
                    <div>
                        <h2 class="font-['Fraunces'] text-lg font-[500] text-surface-900 dark:text-surface-50">Operaciones</h2>
                        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-surface-400 dark:text-surface-600">Accesos rápidos</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                    @role('Administrador')
                        <a href="{{ route('users.index') }}" class="group relative flex flex-col items-start overflow-hidden rounded-lg border border-surface-200 bg-white p-5 transition-all duration-200 hover:border-brand-300 hover:shadow-card-hover sm:p-6 dark:border-surface-800 dark:bg-surface-900 dark:hover:border-brand-700" aria-label="Ir a gestión de usuarios">
                            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600 transition-colors group-hover:bg-brand-100 dark:bg-brand-950/50 dark:text-brand-400 dark:group-hover:bg-brand-900/50">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Usuarios</p>
                            <p class="mt-0.5 text-[11px] text-surface-400 dark:text-surface-500">Roles y cuentas</p>
                            <svg class="absolute bottom-4 right-4 h-4 w-4 text-surface-300 transition-all group-hover:translate-x-0.5 group-hover:text-brand-500 dark:text-surface-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    @endrole

                    <a href="{{ route('inventory.index') }}" class="group relative flex flex-col items-start overflow-hidden rounded-lg border border-surface-200 bg-white p-5 transition-all duration-200 hover:border-amber-300 hover:shadow-card-hover sm:p-6 dark:border-surface-800 dark:bg-surface-900 dark:hover:border-amber-700" aria-label="Ir a inventario">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition-colors group-hover:bg-amber-100 dark:bg-amber-950/50 dark:text-amber-400 dark:group-hover:bg-amber-900/50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Inventario</p>
                        <p class="mt-0.5 text-[11px] text-surface-400 dark:text-surface-500">Stock y productos</p>
                        <svg class="absolute bottom-4 right-4 h-4 w-4 text-surface-300 transition-all group-hover:translate-x-0.5 group-hover:text-amber-500 dark:text-surface-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>

                    <a href="{{ route('billing.index') }}" class="group relative flex flex-col items-start overflow-hidden rounded-lg border border-surface-200 bg-white p-5 transition-all duration-200 hover:border-sky-300 hover:shadow-card-hover sm:p-6 dark:border-surface-800 dark:bg-surface-900 dark:hover:border-sky-700" aria-label="Ir a facturación">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition-colors group-hover:bg-sky-100 dark:bg-sky-950/50 dark:text-sky-400 dark:group-hover:bg-sky-900/50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Facturación</p>
                        <p class="mt-0.5 text-[11px] text-surface-400 dark:text-surface-500">Historial y POS</p>
                        <svg class="absolute bottom-4 right-4 h-4 w-4 text-surface-300 transition-all group-hover:translate-x-0.5 group-hover:text-sky-500 dark:text-surface-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>

                    <a href="{{ route('profile') }}" class="group relative flex flex-col items-start overflow-hidden rounded-lg border border-surface-200 bg-white p-5 transition-all duration-200 hover:border-surface-400 hover:shadow-card-hover sm:p-6 dark:border-surface-800 dark:bg-surface-900 dark:hover:border-surface-600" aria-label="Ir a mi perfil">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-surface-100 text-surface-500 transition-colors group-hover:bg-surface-200 dark:bg-surface-800 dark:text-surface-400 dark:group-hover:bg-surface-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Mi perfil</p>
                        <p class="mt-0.5 text-[11px] text-surface-400 dark:text-surface-500">Cuenta y ajustes</p>
                        <svg class="absolute bottom-4 right-4 h-4 w-4 text-surface-300 transition-all group-hover:translate-x-0.5 group-hover:text-surface-500 dark:text-surface-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </section>

            {{-- Footer micro --}}
            <div class="mt-16 border-t border-surface-200 pt-6 dark:border-surface-800">
                <p class="font-mono text-[9px] uppercase tracking-[0.2em] text-surface-400 dark:text-surface-600">
                    Hedman Garcia Pharmacy · MMXXVI · Tegucigalpa, HN
                </p>
            </div>

        </div>
    </div>
</div>
