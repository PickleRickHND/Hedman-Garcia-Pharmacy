<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('reports.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Reportes
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Reporte de inventario</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @php $s = $this->snapshot; @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Productos</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">{{ $s['total_products'] }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Unidades totales</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">{{ number_format($s['total_units']) }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Valor del inventario</p>
                    <p class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">L. {{ number_format($s['total_value'], 2) }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Alertas</p>
                    <p class="mt-1 text-sm text-surface-700 dark:text-surface-300">
                        <span class="{{ $s['low_stock'] > 0 ? 'text-amber-600' : '' }}">{{ $s['low_stock'] }} bajo</span> ·
                        <span class="{{ $s['out_of_stock'] > 0 ? 'text-red-600' : '' }}">{{ $s['out_of_stock'] }} agotados</span> ·
                        <span class="{{ $s['expired'] > 0 ? 'text-red-600' : '' }}">{{ $s['expired'] }} vencidos</span>
                    </p>
                </x-ui.card>
            </div>

            <x-ui.table>
                <thead class="bg-surface-50 dark:bg-surface-900/50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">SKU</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Producto</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Categoría</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Stock</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Precio</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Valor</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Vence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                    @foreach ($s['products'] as $product)
                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40 {{ $product->is_expired ? 'bg-red-50/50 dark:bg-red-950/10' : ($product->is_low_stock ? 'bg-amber-50/50 dark:bg-amber-950/10' : '') }}">
                            <td class="px-4 py-2 font-mono text-xs text-surface-600 dark:text-surface-400">{{ $product->sku }}</td>
                            <td class="px-4 py-2 text-sm text-surface-900 dark:text-surface-100">{{ $product->name }}</td>
                            <td class="px-4 py-2 text-xs text-surface-600 dark:text-surface-400">{{ $product->category?->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-right text-sm font-semibold {{ $product->is_out_of_stock ? 'text-red-600' : ($product->is_low_stock ? 'text-amber-600' : 'text-surface-900 dark:text-surface-100') }}">{{ $product->stock }}</td>
                            <td class="px-4 py-2 text-right text-sm text-surface-600 dark:text-surface-400">L. {{ number_format((float) $product->price, 2) }}</td>
                            <td class="px-4 py-2 text-right text-sm font-medium text-surface-900 dark:text-surface-100">L. {{ number_format((float) $product->price * $product->stock, 2) }}</td>
                            <td class="px-4 py-2 text-sm">
                                @if ($product->expiration_date)
                                    <span class="{{ $product->is_expired ? 'text-red-600 font-semibold' : ($product->is_expiring_soon ? 'text-amber-600' : 'text-surface-600 dark:text-surface-400') }}">
                                        {{ $product->expiration_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-surface-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        </div>
    </div>
</div>
