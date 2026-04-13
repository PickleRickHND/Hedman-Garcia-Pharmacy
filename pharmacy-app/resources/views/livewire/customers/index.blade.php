<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Clientes</h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Catálogo de clientes con historial de compras.</p>
            </div>
            <a href="{{ route('customers.create') }}">
                <x-ui.button variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo cliente
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>{{ $flashMessage }}</x-ui.alert>
            @endif
            @if (session('customers.flash'))
                <x-ui.alert variant="success" dismissible>{{ session('customers.flash') }}</x-ui.alert>
            @endif

            <x-ui.card padding="sm">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, RTN, teléfono o email..." class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" />
            </x-ui.card>

            @if ($customers->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state title="Sin clientes" description="Crea el primer cliente para vincular facturas.">
                        <a href="{{ route('customers.create') }}"><x-ui.button variant="primary">Nuevo cliente</x-ui.button></a>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Cliente</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">RTN</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Teléfono</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Facturas</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($customers as $customer)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $customer->name }}</p>
                                    @if ($customer->email)
                                        <p class="text-xs text-surface-500 dark:text-surface-400">{{ $customer->email }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400 font-mono">{{ $customer->rtn ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $customer->phone ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $customer->invoices_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('customers.show', $customer) }}"><x-ui.button variant="ghost" size="sm">Ver</x-ui.button></a>
                                        <a href="{{ route('customers.edit', $customer) }}"><x-ui.button variant="ghost" size="sm">Editar</x-ui.button></a>
                                        @role('Administrador')
                                            <x-ui.button variant="ghost" size="sm" class="text-danger hover:bg-red-50 dark:hover:bg-red-950/30" wire:click="delete({{ $customer->id }})" wire:confirm="¿Eliminar «{{ $customer->name }}»?">Eliminar</x-ui.button>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
                <div class="mt-4">{{ $customers->links() }}</div>
            @endif
        </div>
    </div>
</div>
