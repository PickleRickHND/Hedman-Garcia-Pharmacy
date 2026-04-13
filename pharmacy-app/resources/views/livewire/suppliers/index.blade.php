<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Proveedores</h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Gestión de proveedores farmacéuticos.
                </p>
            </div>
            <a href="{{ route('suppliers.create') }}">
                <x-ui.button variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo proveedor
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>{{ $flashMessage }}</x-ui.alert>
            @endif

            @if (session('suppliers.flash'))
                <x-ui.alert variant="success" dismissible>{{ session('suppliers.flash') }}</x-ui.alert>
            @endif

            <x-ui.card padding="sm">
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nombre, contacto, teléfono..."
                    class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                />
            </x-ui.card>

            @if ($suppliers->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state
                        title="Sin proveedores"
                        description="Crea el primer proveedor para asociarlo a productos."
                    >
                        <a href="{{ route('suppliers.create') }}">
                            <x-ui.button variant="primary">Nuevo proveedor</x-ui.button>
                        </a>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Proveedor</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Contacto</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Teléfono</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Productos</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Estado</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($suppliers as $supplier)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $supplier->name }}</p>
                                    @if ($supplier->email)
                                        <p class="text-xs text-surface-500 dark:text-surface-400">{{ $supplier->email }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $supplier->contact_name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400 font-mono">{{ $supplier->phone ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $supplier->products_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($supplier->is_active)
                                        <x-ui.badge variant="success" size="sm">Activo</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" size="sm">Inactivo</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('suppliers.edit', $supplier) }}">
                                            <x-ui.button variant="ghost" size="sm">Editar</x-ui.button>
                                        </a>
                                        <x-ui.button variant="ghost" size="sm" wire:click="toggleActive({{ $supplier->id }})">
                                            {{ $supplier->is_active ? 'Desactivar' : 'Activar' }}
                                        </x-ui.button>
                                        <x-ui.button
                                            variant="ghost"
                                            size="sm"
                                            class="text-danger hover:bg-red-50 dark:hover:bg-red-950/30"
                                            wire:click="delete({{ $supplier->id }})"
                                            wire:confirm="¿Eliminar «{{ $supplier->name }}»? Los productos quedarán sin proveedor."
                                        >
                                            Eliminar
                                        </x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>

                <div class="mt-4">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
