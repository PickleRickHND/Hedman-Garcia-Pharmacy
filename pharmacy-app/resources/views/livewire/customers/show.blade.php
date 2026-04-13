<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('customers.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Volver a clientes
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">{{ $customer->name }}</h1>
            </div>
            <a href="{{ route('customers.edit', $customer) }}">
                <x-ui.button variant="secondary">Editar</x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Datos del cliente --}}
            <x-ui.card>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">RTN</p>
                        <p class="text-sm text-surface-900 dark:text-surface-100">{{ $customer->rtn ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Teléfono</p>
                        <p class="text-sm text-surface-900 dark:text-surface-100">{{ $customer->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Email</p>
                        <p class="text-sm text-surface-900 dark:text-surface-100">{{ $customer->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Dirección</p>
                        <p class="text-sm text-surface-900 dark:text-surface-100">{{ $customer->address ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Total compras</p>
                        <p class="text-sm font-semibold text-brand-600 dark:text-brand-400">L. {{ number_format($totalSpent, 2) }}</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Historial de facturas --}}
            <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50 mt-6">Historial de compras</h2>

            @if ($invoices->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state title="Sin facturas" description="Este cliente aún no tiene facturas vinculadas." />
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Número</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Fecha</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Vendedor</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Total</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($invoices as $invoice)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $invoice->invoice_number }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $invoice->issued_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $invoice->seller?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">L. {{ number_format((float) $invoice->total, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('billing.show', $invoice) }}"><x-ui.button variant="ghost" size="sm">Ver</x-ui.button></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
                <div class="mt-4">{{ $invoices->links() }}</div>
            @endif
        </div>
    </div>
</div>
