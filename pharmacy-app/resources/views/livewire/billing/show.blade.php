<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('billing.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Volver al histórico
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50 font-mono">{{ $invoice->invoice_number }}</h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Emitida el {{ $invoice->issued_at->format('d M Y H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('billing.pdf', $invoice) }}" target="_blank">
                    <x-ui.button variant="secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar PDF
                    </x-ui.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <x-ui.card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-2">Cliente</h3>
                        <p class="text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $invoice->customer_name }}</p>
                        @if ($invoice->customer_rtn)
                            <p class="text-sm text-surface-600 dark:text-surface-400">RTN: {{ $invoice->customer_rtn }}</p>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-2">Pago</h3>
                        <p class="text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $invoice->paymentMethod?->name ?? '—' }}</p>
                        <p class="text-sm text-surface-600 dark:text-surface-400">Vendedor: {{ $invoice->seller?->name ?? '—' }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card padding="none">
                <div class="px-6 pt-6 pb-3">
                    <h3 class="font-semibold text-surface-900 dark:text-surface-100">Productos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-800">
                        <thead class="bg-surface-50 dark:bg-surface-900/50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Producto</th>
                                <th class="px-6 py-2 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Precio</th>
                                <th class="px-6 py-2 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Cant.</th>
                                <th class="px-6 py-2 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td class="px-6 py-3">
                                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $item->product_name }}</p>
                                        <p class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ $item->product_sku }}</p>
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm text-surface-600 dark:text-surface-400">L. {{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-6 py-3 text-right text-sm text-surface-600 dark:text-surface-400">{{ $item->quantity }}</td>
                                    <td class="px-6 py-3 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">L. {{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-800 bg-surface-50 dark:bg-surface-900/50">
                    <dl class="ml-auto max-w-xs space-y-2">
                        <div class="flex justify-between text-sm">
                            <dt class="text-surface-600 dark:text-surface-400">Subtotal</dt>
                            <dd class="text-surface-900 dark:text-surface-100 font-medium">L. {{ number_format((float) $invoice->subtotal, 2) }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-surface-600 dark:text-surface-400">ISV (15%)</dt>
                            <dd class="text-surface-900 dark:text-surface-100 font-medium">L. {{ number_format((float) $invoice->tax, 2) }}</dd>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-surface-300 dark:border-surface-700">
                            <dt class="text-base font-bold text-surface-900 dark:text-surface-50">Total</dt>
                            <dd class="text-lg font-bold text-brand-600 dark:text-brand-400">L. {{ number_format((float) $invoice->total, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
