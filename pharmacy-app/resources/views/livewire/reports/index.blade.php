<div>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Reportes</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Centro de reportes para análisis del negocio.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('reports.sales') }}" class="group">
                    <x-ui.card hover>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600 mb-4 dark:bg-brand-950/50 dark:text-brand-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Ventas por periodo</p>
                        <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Ingresos, desglose por método de pago, promedio diario.</p>
                    </x-ui.card>
                </a>

                <a href="{{ route('reports.products') }}" class="group">
                    <x-ui.card hover>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 mb-4 dark:bg-amber-950/50 dark:text-amber-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Productos más vendidos</p>
                        <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Top productos por cantidad o ingresos.</p>
                    </x-ui.card>
                </a>

                <a href="{{ route('reports.inventory') }}" class="group">
                    <x-ui.card hover>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-600 mb-4 dark:bg-sky-950/50 dark:text-sky-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        </div>
                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">Inventario actual</p>
                        <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Snapshot del stock, valor total, alertas.</p>
                    </x-ui.card>
                </a>
            </div>
        </div>
    </div>
</div>
