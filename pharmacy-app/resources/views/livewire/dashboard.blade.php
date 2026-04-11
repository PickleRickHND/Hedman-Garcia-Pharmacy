<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">
                    Bienvenido, {{ auth()->user()->name }}
                </h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Resumen operativo de la farmacia · {{ now()->format('d M Y') }}
                </p>
            </div>
            <x-ui.badge variant="brand" size="lg">
                {{ auth()->user()->primary_role ?? 'Sin rol' }}
            </x-ui.badge>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Grupo: Usuarios --}}
            <section>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-3">
                    Personal
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <x-ui.card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Total usuarios</p>
                                <p class="mt-2 text-3xl font-bold text-surface-900 dark:text-surface-50">{{ $metrics['users_total'] }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-600 dark:text-brand-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Administradores</p>
                                <p class="mt-2 text-3xl font-bold text-surface-900 dark:text-surface-50">{{ $metrics['users_admins'] }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 dark:text-sky-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Cajeros</p>
                                <p class="mt-2 text-3xl font-bold text-surface-900 dark:text-surface-50">{{ $metrics['users_cashiers'] }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </section>

            {{-- Grupo: Inventario --}}
            <section>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-3">
                    Inventario
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-ui.card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Productos totales</p>
                                <p class="mt-2 text-3xl font-bold text-surface-900 dark:text-surface-50">{{ $metrics['products_total'] }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-600 dark:text-brand-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        </div>
                    </x-ui.card>
                    <x-ui.card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Stock bajo</p>
                                <p class="mt-2 text-3xl font-bold {{ $metrics['low_stock'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-surface-900 dark:text-surface-50' }}">{{ $metrics['low_stock'] }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                        </div>
                    </x-ui.card>
                    <x-ui.card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Próximos a vencer</p>
                                <p class="mt-2 text-3xl font-bold {{ $metrics['expiring_soon'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-surface-900 dark:text-surface-50' }}">{{ $metrics['expiring_soon'] }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </section>

            {{-- Grupo: Facturación (placeholder Fase 4) --}}
            <section>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-3 flex items-center gap-2">
                    Facturación
                    <x-ui.badge variant="warning" size="sm">Fase 4</x-ui.badge>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui.card>
                        <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Facturas hoy</p>
                        <p class="mt-2 text-3xl font-bold text-surface-400 dark:text-surface-600">—</p>
                    </x-ui.card>
                    <x-ui.card>
                        <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Ingresos del día</p>
                        <p class="mt-2 text-3xl font-bold text-surface-400 dark:text-surface-600">L. —</p>
                    </x-ui.card>
                </div>
            </section>

            {{-- Accesos rápidos --}}
            <section>
                <h2 class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-3">
                    Accesos rápidos
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @role('Administrador')
                        <a href="{{ route('users.index') }}" class="block card card-hover p-6 text-center">
                            <div class="w-10 h-10 mx-auto mb-3 rounded-lg bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-600 dark:text-brand-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Usuarios</p>
                        </a>
                    @endrole

                    <a href="{{ route('inventory.index') }}" class="block card card-hover p-6 text-center">
                        <div class="w-10 h-10 mx-auto mb-3 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Inventario</p>
                    </a>

                    <a href="{{ route('billing.index') }}" class="block card card-hover p-6 text-center">
                        <div class="w-10 h-10 mx-auto mb-3 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 dark:text-sky-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Facturación</p>
                    </a>

                    <a href="{{ route('profile') }}" class="block card card-hover p-6 text-center">
                        <div class="w-10 h-10 mx-auto mb-3 rounded-lg bg-surface-200 dark:bg-surface-800 flex items-center justify-center text-surface-600 dark:text-surface-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Mi perfil</p>
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
