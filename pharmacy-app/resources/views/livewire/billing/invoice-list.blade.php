<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Facturación</h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Histórico de facturas emitidas y accesos rápidos al POS.
                </p>
            </div>
            <a href="{{ route('billing.create') }}">
                <x-ui.button variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nueva factura
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('billing.flash'))
                <x-ui.alert variant="success" dismissible>{{ session('billing.flash') }}</x-ui.alert>
            @endif

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Facturas hoy</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">{{ $summary['today_count'] }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Ingresos hoy</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">L. {{ number_format($summary['today_revenue'], 2) }}</p>
                </x-ui.card>
                <x-ui.card padding="sm">
                    <p class="text-xs font-medium text-surface-500 dark:text-surface-400">Ingresos de la semana</p>
                    <p class="mt-1 text-2xl font-bold text-surface-900 dark:text-surface-50">L. {{ number_format($summary['week_revenue'], 2) }}</p>
                </x-ui.card>
            </div>

            {{-- Filtros --}}
            <x-ui.card padding="sm">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Buscar por número, cliente o RTN..."
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        />
                    </div>
                    <div class="sm:w-48">
                        <select
                            wire:model.live="paymentMethodFilter"
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        >
                            <option value="">Todos los métodos</option>
                            @foreach ($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:w-40">
                        <select
                            wire:model.live="dateFilter"
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        >
                            <option value="">Todas las fechas</option>
                            <option value="today">Hoy</option>
                            <option value="week">Esta semana</option>
                            <option value="month">Este mes</option>
                        </select>
                    </div>
                </div>
            </x-ui.card>

            {{-- Tabla --}}
            @if ($invoices->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state
                        title="Sin facturas"
                        description="Aún no se han emitido facturas con estos filtros."
                    >
                        <a href="{{ route('billing.create') }}">
                            <x-ui.button variant="primary">Emitir primera factura</x-ui.button>
                        </a>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Número</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Fecha</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Cliente</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Vendedor</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Método</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Total</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($invoices as $invoice)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $invoice->invoice_number }}</span>
                                        @if ($invoice->is_voided)
                                            <x-ui.badge variant="danger" size="sm">Anulada</x-ui.badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-surface-600 dark:text-surface-400">{{ $invoice->issued_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $invoice->customer_name }}</p>
                                    @if ($invoice->customer_rtn)
                                        <p class="text-xs text-surface-500 dark:text-surface-400">{{ $invoice->customer_rtn }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $invoice->seller?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $invoice->paymentMethod?->name ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-surface-900 dark:text-surface-100">L. {{ number_format((float) $invoice->total, 2) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <a href="{{ route('billing.show', $invoice) }}">
                                        <x-ui.button variant="ghost" size="sm">Ver</x-ui.button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>

                <div class="mt-4">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
