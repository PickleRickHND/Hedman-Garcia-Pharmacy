<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('reports.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Reportes
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Productos más vendidos</h1>
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
                    <div class="sm:w-40">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Ordenar por</label>
                        <select wire:model.live="sortBy" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100">
                            <option value="quantity">Cantidad</option>
                            <option value="revenue">Ingresos</option>
                        </select>
                    </div>
                </div>
            </x-ui.card>

            @if ($this->topProducts->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state title="Sin datos" description="No hay ventas en el periodo seleccionado." />
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400 w-12">#</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Producto</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Cantidad</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Ingresos</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400 w-48"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @php $maxVal = $this->topProducts->max($sortBy === 'revenue' ? 'total_revenue' : 'total_quantity') ?: 1; @endphp
                        @foreach ($this->topProducts as $i => $p)
                            @php $pct = ($sortBy === 'revenue' ? (float) $p->total_revenue : (int) $p->total_quantity) / $maxVal * 100; @endphp
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3 text-center text-sm font-bold text-surface-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $p->product_name }}</p>
                                    <p class="text-xs text-surface-500 font-mono">{{ $p->product_sku }}</p>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">{{ number_format((int) $p->total_quantity) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">L. {{ number_format((float) $p->total_revenue, 2) }}</td>
                                <td class="px-4 py-3">
                                    <div class="w-full bg-surface-200 dark:bg-surface-800 rounded-full h-2">
                                        <div class="bg-brand-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            @endif
        </div>
    </div>
</div>
