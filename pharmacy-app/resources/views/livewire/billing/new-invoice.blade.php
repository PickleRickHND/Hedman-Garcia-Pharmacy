<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('billing.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Volver al histórico
                </a>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Nueva factura</h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Agrega productos al carrito, captura los datos del cliente y emite la factura.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($flashMessage)
                <div class="mb-4">
                    <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>{{ $flashMessage }}</x-ui.alert>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Columna izquierda: buscador + carrito --}}
                <div class="lg:col-span-2 space-y-4">
                    <x-ui.card>
                        <x-slot name="header">
                            <h2 class="font-semibold text-surface-900 dark:text-surface-100">Buscar productos</h2>
                            <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">Solo productos con stock disponible.</p>
                        </x-slot>

                        <div class="relative">
                            <input
                                type="search"
                                wire:model.live.debounce.250ms="search"
                                placeholder="Buscar por nombre, SKU, descripción..."
                                class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                            />

                            @if (strlen($search) >= 2)
                                <div class="mt-3 border border-surface-200 dark:border-surface-700 rounded-lg divide-y divide-surface-200 dark:divide-surface-700 max-h-80 overflow-y-auto">
                                    @forelse ($this->searchResults as $product)
                                        <button
                                            type="button"
                                            wire:click="addProduct({{ $product->id }})"
                                            class="w-full text-left px-4 py-3 hover:bg-surface-50 dark:hover:bg-surface-800 flex items-center justify-between gap-3"
                                        >
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-surface-900 dark:text-surface-100 truncate">{{ $product->name }}</p>
                                                <p class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ $product->sku }}</p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <p class="text-sm font-semibold text-surface-900 dark:text-surface-100">L. {{ number_format((float) $product->price, 2) }}</p>
                                                <p class="text-xs text-surface-500 dark:text-surface-400">Stock: {{ $product->stock }}</p>
                                            </div>
                                        </button>
                                    @empty
                                        <div class="px-4 py-6 text-center text-sm text-surface-500 dark:text-surface-400">
                                            Sin resultados para «{{ $search }}»
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    </x-ui.card>

                    {{-- Carrito --}}
                    <x-ui.card padding="none">
                        <div class="px-6 pt-6 pb-4 flex items-center justify-between">
                            <h2 class="font-semibold text-surface-900 dark:text-surface-100">Carrito</h2>
                            @if (count($items) > 0)
                                <button type="button" wire:click="clearCart" class="text-xs text-surface-500 hover:text-danger">
                                    Vaciar
                                </button>
                            @endif
                        </div>

                        @if (count($items) === 0)
                            <x-ui.empty-state
                                title="El carrito está vacío"
                                description="Usa el buscador para agregar productos."
                            />
                        @else
                            <div class="divide-y divide-surface-200 dark:divide-surface-800">
                                @foreach ($items as $index => $item)
                                    <div class="px-6 py-4 flex items-center gap-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $item['name'] }}</p>
                                            <p class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ $item['sku'] }} · L. {{ number_format($item['unit_price'], 2) }}</p>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button type="button" wire:click="decrementItem({{ $index }})" class="w-7 h-7 rounded-md border border-surface-300 dark:border-surface-700 text-surface-600 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800">−</button>
                                            <span class="w-8 text-center text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $item['quantity'] }}</span>
                                            <button type="button" wire:click="incrementItem({{ $index }})" class="w-7 h-7 rounded-md border border-surface-300 dark:border-surface-700 text-surface-600 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800">+</button>
                                        </div>

                                        <div class="w-24 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">
                                            L. {{ number_format($item['quantity'] * $item['unit_price'], 2) }}
                                        </div>

                                        <button type="button" wire:click="removeItem({{ $index }})" class="text-surface-400 hover:text-danger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </x-ui.card>
                </div>

                {{-- Columna derecha: datos + totales + emitir --}}
                <div class="space-y-4">
                    <x-ui.card>
                        <x-slot name="header">
                            <h2 class="font-semibold text-surface-900 dark:text-surface-100">Cliente</h2>
                        </x-slot>

                        <div class="space-y-4">
                            <x-ui.input
                                label="Nombre"
                                name="customer_name"
                                wire:model="customer_name"
                                :error="$errors->first('customer_name')"
                                required
                            />

                            <x-ui.input
                                label="RTN (opcional)"
                                name="customer_rtn"
                                wire:model="customer_rtn"
                                :error="$errors->first('customer_rtn')"
                                hint="Ej: 0801-1985-12345"
                            />

                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                                    Método de pago <span class="text-danger">*</span>
                                </label>
                                <select
                                    wire:model="payment_method_id"
                                    class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                                >
                                    <option value="">Selecciona...</option>
                                    @foreach ($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method_id')<p class="text-xs text-danger">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card>
                        <x-slot name="header">
                            <h2 class="font-semibold text-surface-900 dark:text-surface-100">Resumen</h2>
                        </x-slot>

                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-surface-600 dark:text-surface-400">Subtotal</dt>
                                <dd class="text-surface-900 dark:text-surface-100 font-medium">L. {{ number_format($this->totals['subtotal'], 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-surface-600 dark:text-surface-400">ISV (15%)</dt>
                                <dd class="text-surface-900 dark:text-surface-100 font-medium">L. {{ number_format($this->totals['tax'], 2) }}</dd>
                            </div>
                            <div class="flex justify-between pt-3 mt-2 border-t border-surface-200 dark:border-surface-800">
                                <dt class="text-base font-bold text-surface-900 dark:text-surface-50">Total</dt>
                                <dd class="text-xl font-bold text-brand-600 dark:text-brand-400">L. {{ number_format($this->totals['total'], 2) }}</dd>
                            </div>
                        </dl>

                        <x-slot name="footer">
                            <button
                                type="button"
                                wire:click="issue"
                                @if (count($items) === 0) disabled @endif
                                class="w-full inline-flex items-center justify-center gap-2 font-medium rounded-md px-4 py-2 text-sm bg-brand-600 text-white hover:bg-brand-700 active:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-500 shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Emitir factura
                            </button>
                        </x-slot>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>
</div>
