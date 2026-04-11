<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Inventario</h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Gestión de productos farmacéuticos, stock y fechas de vencimiento.
                </p>
            </div>
            @role('Administrador')
                <a href="{{ route('products.create') }}">
                    <x-ui.button variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nuevo producto
                    </x-ui.button>
                </a>
            @endrole
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>
                    {{ $flashMessage }}
                </x-ui.alert>
            @endif

            {{-- Summary cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Productos</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">{{ $summary['total'] }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Stock bajo</p>
                    <p class="mt-1 text-2xl font-bold {{ $summary['low_stock'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-surface-900 dark:text-surface-50' }}">{{ $summary['low_stock'] }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Próximos a vencer</p>
                    <p class="mt-1 text-2xl font-bold {{ $summary['expiring'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-surface-900 dark:text-surface-50' }}">{{ $summary['expiring'] }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Vencidos</p>
                    <p class="mt-1 text-2xl font-bold {{ $summary['expired'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-surface-900 dark:text-surface-50' }}">{{ $summary['expired'] }}</p>
                </x-ui.card>
            </div>

            {{-- Filtros --}}
            <x-ui.card padding="sm">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Buscar por nombre, SKU, descripción..."
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        />
                    </div>
                    <div class="sm:w-56">
                        <select
                            wire:model.live="stockFilter"
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        >
                            <option value="">Todos los productos</option>
                            <option value="low">Stock bajo</option>
                            <option value="out">Sin stock</option>
                            <option value="expiring">Próximos a vencer</option>
                            <option value="expired">Vencidos</option>
                        </select>
                    </div>
                </div>
            </x-ui.card>

            {{-- Tabla --}}
            @if ($products->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state
                        title="Sin productos"
                        description="Ajusta los filtros o crea un nuevo producto."
                    >
                        @role('Administrador')
                            <a href="{{ route('products.create') }}">
                                <x-ui.button variant="primary">Nuevo producto</x-ui.button>
                            </a>
                        @endrole
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">
                                <button wire:click="sortBy('sku')" class="flex items-center gap-1 hover:text-surface-900 dark:hover:text-surface-100">
                                    SKU
                                    @if ($sortField === 'sku')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                                </button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">
                                <button wire:click="sortBy('name')" class="flex items-center gap-1 hover:text-surface-900 dark:hover:text-surface-100">
                                    Producto
                                    @if ($sortField === 'name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                                </button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">
                                <button wire:click="sortBy('stock')" class="flex items-center gap-1 ml-auto hover:text-surface-900 dark:hover:text-surface-100">
                                    Stock
                                    @if ($sortField === 'stock')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                                </button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">
                                <button wire:click="sortBy('price')" class="flex items-center gap-1 ml-auto hover:text-surface-900 dark:hover:text-surface-100">
                                    Precio
                                    @if ($sortField === 'price')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                                </button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">
                                <button wire:click="sortBy('expiration_date')" class="flex items-center gap-1 hover:text-surface-900 dark:hover:text-surface-100">
                                    Vencimiento
                                    @if ($sortField === 'expiration_date')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                                </button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($products as $product)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-mono text-xs text-surface-600 dark:text-surface-400">{{ $product->sku }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $product->name }}</p>
                                            @if ($product->presentation)
                                                <p class="text-xs text-surface-500 dark:text-surface-400">{{ $product->presentation }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $product->stock }}</span>
                                        @if ($product->is_out_of_stock)
                                            <x-ui.badge variant="danger" size="sm">Agotado</x-ui.badge>
                                        @elseif ($product->is_low_stock)
                                            <x-ui.badge variant="warning" size="sm">Bajo</x-ui.badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <span class="text-sm text-surface-900 dark:text-surface-100">L. {{ number_format((float) $product->price, 2) }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($product->expiration_date)
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-surface-700 dark:text-surface-300">{{ $product->expiration_date->format('d M Y') }}</span>
                                            @if ($product->is_expired)
                                                <x-ui.badge variant="danger" size="sm">Vencido</x-ui.badge>
                                            @elseif ($product->is_expiring_soon)
                                                <x-ui.badge variant="warning" size="sm">Por vencer</x-ui.badge>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-surface-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1">
                                        @role('Administrador')
                                            <a href="{{ route('products.edit', $product) }}">
                                                <x-ui.button variant="ghost" size="sm">Editar</x-ui.button>
                                            </a>
                                            <x-ui.button
                                                variant="ghost"
                                                size="sm"
                                                class="text-danger hover:bg-red-50 dark:hover:bg-red-950/30"
                                                wire:click="deleteProduct({{ $product->id }})"
                                                wire:confirm="¿Eliminar «{{ $product->name }}»? El producto se podrá restaurar después."
                                            >
                                                Eliminar
                                            </x-ui.button>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
