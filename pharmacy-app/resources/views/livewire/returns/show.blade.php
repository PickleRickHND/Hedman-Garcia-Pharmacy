<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('returns.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a devoluciones
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50 font-mono">{{ $returnOrder->return_number }}</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                Procesada el {{ $returnOrder->processed_at?->format('d M Y H:i') }} por {{ $returnOrder->processedBy?->name }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('returns.flash'))
                <x-ui.alert variant="success" dismissible>{{ session('returns.flash') }}</x-ui.alert>
            @endif

            <x-ui.card>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Factura origen</p>
                        <a href="{{ route('billing.show', $returnOrder->invoice) }}" class="text-sm font-mono text-brand-600 dark:text-brand-400 hover:underline">
                            {{ $returnOrder->invoice?->invoice_number }}
                        </a>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Motivo</p>
                        <p class="text-sm text-surface-900 dark:text-surface-100">{{ $returnOrder->reason }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Total reembolso</p>
                        <p class="text-sm font-bold text-brand-600 dark:text-brand-400">L. {{ number_format((float) $returnOrder->total_refund, 2) }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card padding="none">
                <div class="px-6 pt-6 pb-3">
                    <h3 class="font-semibold text-surface-900 dark:text-surface-100">Productos devueltos</h3>
                </div>
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-2 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Producto</th>
                            <th scope="col" class="px-6 py-2 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Cant.</th>
                            <th scope="col" class="px-6 py-2 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Precio</th>
                            <th scope="col" class="px-6 py-2 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Subtotal</th>
                            <th scope="col" class="px-6 py-2 text-center text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Reingresado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($returnOrder->items as $item)
                            <tr>
                                <td class="px-6 py-3">
                                    <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $item->product?->name ?? '—' }}</p>
                                    <p class="text-xs text-surface-500 font-mono">{{ $item->product?->sku ?? '' }}</p>
                                </td>
                                <td class="px-6 py-3 text-right text-sm">{{ $item->quantity }}</td>
                                <td class="px-6 py-3 text-right text-sm">L. {{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-6 py-3 text-right text-sm font-semibold">L. {{ number_format((float) $item->subtotal, 2) }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if ($item->restock)
                                        <x-ui.badge variant="success" size="sm">Sí</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" size="sm">No</x-ui.badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            </x-ui.card>
        </div>
    </div>
</div>
