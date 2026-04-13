<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('reports.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Reportes
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Ventas por periodo</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <x-ui.card padding="sm">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Desde</label>
                        <input type="date" wire:model.live="dateFrom" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" />
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Hasta</label>
                        <input type="date" wire:model.live="dateTo" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" />
                    </div>
                </div>
            </x-ui.card>

            @php $r = $this->report; @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Facturas</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">{{ $r['total_invoices'] }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Ingresos totales</p>
                    <p class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">L. {{ number_format($r['total_revenue'], 2) }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Descuentos</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">L. {{ number_format($r['total_discount'], 2) }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Promedio diario</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">L. {{ number_format($r['daily_average'], 2) }}</p>
                </x-ui.card>
            </div>

            @if ($r['by_payment_method']->isNotEmpty())
                <x-ui.card>
                    <x-slot name="header">
                        <h2 class="font-semibold text-surface-900 dark:text-surface-100">Desglose por método de pago</h2>
                    </x-slot>

                    <div class="space-y-3">
                        @foreach ($r['by_payment_method'] as $pm)
                            @php $pct = $r['total_revenue'] > 0 ? ($pm['total'] / $r['total_revenue']) * 100 : 0; @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-surface-700 dark:text-surface-300">{{ $pm['method'] }} ({{ $pm['count'] }})</span>
                                    <span class="font-semibold text-surface-900 dark:text-surface-100">L. {{ number_format($pm['total'], 2) }}</span>
                                </div>
                                <div class="w-full bg-surface-200 dark:bg-surface-800 rounded-full h-2">
                                    <div class="bg-brand-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>
</div>
