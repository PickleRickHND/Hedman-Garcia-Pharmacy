<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('billing.show', $invoice) }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a factura {{ $invoice->invoice_number }}
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Devolución de {{ $invoice->invoice_number }}</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Selecciona los productos y cantidades a devolver.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>{{ $flashMessage }}</x-ui.alert>
            @endif

            <form wire:submit="submit">
                <x-ui.card>
                    <x-slot name="header">
                        <h2 class="font-semibold text-surface-900 dark:text-surface-100">Productos a devolver</h2>
                    </x-slot>

                    <div class="divide-y divide-surface-200 dark:divide-surface-800">
                        @foreach ($returnItems as $itemId => $item)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $item['product_name'] }}</p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ $item['product_sku'] }} · L. {{ number_format($item['unit_price'], 2) }} · Max: {{ $item['max_qty'] }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if ($item['max_qty'] > 0)
                                        <input
                                            type="number"
                                            wire:model="returnItems.{{ $itemId }}.quantity"
                                            min="0"
                                            max="{{ $item['max_qty'] }}"
                                            class="w-20 rounded-md border-surface-300 bg-white text-sm text-right shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                                        />
                                        <label class="flex items-center gap-1.5 text-xs text-surface-600 dark:text-surface-400">
                                            <input type="checkbox" wire:model="returnItems.{{ $itemId }}.restock" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500 dark:border-surface-700" />
                                            Reingresar
                                        </label>
                                    @else
                                        <span class="text-xs text-surface-400">Ya devuelto</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>

                <x-ui.card class="mt-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Motivo de devolución <span class="text-danger">*</span></label>
                            <textarea
                                wire:model="reason"
                                rows="3"
                                class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                                placeholder="Explica el motivo..."
                            ></textarea>
                            @error('reason')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('billing.show', $invoice) }}"><x-ui.button type="button" variant="secondary">Cancelar</x-ui.button></a>
                            <x-ui.button type="submit" variant="primary">Procesar devolución</x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            </form>
        </div>
    </div>
</div>
