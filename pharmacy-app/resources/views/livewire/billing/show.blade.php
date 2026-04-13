<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('billing.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Volver al histórico
                </a>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50 font-mono">{{ $invoice->invoice_number }}</h1>
                    @if ($invoice->is_voided)
                        <x-ui.badge variant="danger">ANULADA</x-ui.badge>
                    @else
                        <x-ui.badge variant="success">Emitida</x-ui.badge>
                    @endif
                </div>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Emitida el {{ $invoice->issued_at->format('d M Y H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('billing.pdf', $invoice) }}" target="_blank">
                    <x-ui.button variant="secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        PDF
                    </x-ui.button>
                </a>
                @if (!$invoice->is_voided)
                    @role('Administrador')
                        <a href="{{ route('returns.create', $invoice) }}">
                            <x-ui.button variant="secondary">Devolución</x-ui.button>
                        </a>
                        <x-ui.button variant="danger" wire:click="openVoidModal">Anular</x-ui.button>
                    @endrole
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>{{ $flashMessage }}</x-ui.alert>
            @endif

            @if ($invoice->is_voided)
                <x-ui.alert variant="danger" title="Factura anulada">
                    <p>Anulada el {{ $invoice->voided_at->format('d M Y H:i') }} por {{ $invoice->voidedByUser?->name ?? '—' }}</p>
                    <p class="mt-1"><strong>Motivo:</strong> {{ $invoice->void_reason }}</p>
                </x-ui.alert>
            @endif

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
                                <th class="px-6 py-2 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Desc.</th>
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
                                    <td class="px-6 py-3 text-right text-sm text-surface-600 dark:text-surface-400">
                                        @if ((float) $item->discount_percent > 0)
                                            <span class="text-green-600 dark:text-green-400">{{ number_format((float) $item->discount_percent, 0) }}%</span>
                                        @else
                                            —
                                        @endif
                                    </td>
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
                        @if ((float) $invoice->discount_total > 0)
                            <div class="flex justify-between text-sm">
                                <dt class="text-green-600 dark:text-green-400">Descuento</dt>
                                <dd class="text-green-600 dark:text-green-400 font-medium">- L. {{ number_format((float) $invoice->discount_total, 2) }}</dd>
                            </div>
                        @endif
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

    {{-- Modal de anulación --}}
    @if ($showVoidModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="cancelVoid">
            <div class="bg-white dark:bg-surface-900 rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
                <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-50">Anular factura {{ $invoice->invoice_number }}</h3>
                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                    Esta acción restaurará el stock de todos los productos. No se puede revertir.
                </p>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Motivo de anulación <span class="text-danger">*</span></label>
                    <textarea
                        wire:model="voidReason"
                        rows="3"
                        class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        placeholder="Explica por qué se anula esta factura..."
                    ></textarea>
                    @error('voidReason')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <x-ui.button variant="secondary" wire:click="cancelVoid">Cancelar</x-ui.button>
                    <x-ui.button variant="danger" wire:click="confirmVoid">Confirmar anulación</x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
