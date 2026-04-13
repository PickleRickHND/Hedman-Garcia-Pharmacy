<div>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Kardex de inventario</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                Historial completo de movimientos de stock: ventas, compras, devoluciones, ajustes y mermas.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Filtros --}}
            <x-ui.card padding="sm">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="sm:w-56">
                        <select wire:model.live="productFilter" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100">
                            <option value="">Todos los productos</option>
                            @foreach ($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->sku }} — {{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:w-40">
                        <select wire:model.live="typeFilter" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100">
                            <option value="">Todos los tipos</option>
                            @foreach ($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:w-40">
                        <input type="date" wire:model.live="dateFrom" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" placeholder="Desde" />
                    </div>
                    <div class="sm:w-40">
                        <input type="date" wire:model.live="dateTo" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" placeholder="Hasta" />
                    </div>
                </div>
            </x-ui.card>

            {{-- Tabla --}}
            @if ($movements->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state
                        title="Sin movimientos"
                        description="No hay movimientos de stock registrados con estos filtros."
                    />
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Fecha</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Producto</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Tipo</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Cant.</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Antes</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Después</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Razón</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($movements as $mov)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-surface-600 dark:text-surface-400">
                                    {{ $mov->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $mov->product?->name ?? '—' }}</p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ $mov->product?->sku ?? '' }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-ui.badge :variant="$mov->badge_color" size="sm">{{ $mov->label }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold {{ $mov->quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $mov->quantity > 0 ? '+' : '' }}{{ $mov->quantity }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm text-surface-600 dark:text-surface-400">{{ $mov->stock_before }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $mov->stock_after }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400 max-w-48 truncate">{{ $mov->reason ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $mov->user?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>

                <div class="mt-4">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
