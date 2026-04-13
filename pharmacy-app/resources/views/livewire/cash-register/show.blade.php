<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('cash-register.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a caja
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Corte #{{ $register->id }}</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                Cerrado el {{ $register->closed_at?->format('d M Y H:i') }} por {{ $register->user?->name }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('cash.flash'))
                <x-ui.alert variant="success" dismissible>{{ session('cash.flash') }}</x-ui.alert>
            @endif

            <x-ui.card>
                <x-slot name="header">
                    <h2 class="font-semibold text-surface-900 dark:text-surface-100">Resumen del corte</h2>
                </x-slot>

                <dl class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Periodo</dt>
                        <dd class="text-surface-900 dark:text-surface-100">{{ $register->opened_at->format('d/m H:i') }} — {{ $register->closed_at?->format('d/m H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Facturas emitidas</dt>
                        <dd class="text-surface-900 dark:text-surface-100 font-semibold">{{ $register->invoices_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1">Facturas anuladas</dt>
                        <dd class="text-surface-900 dark:text-surface-100">{{ $register->voided_count }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            <x-ui.card>
                <x-slot name="header">
                    <h2 class="font-semibold text-surface-900 dark:text-surface-100">Desglose por método</h2>
                </x-slot>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-surface-600 dark:text-surface-400">Efectivo</dt>
                        <dd class="font-medium text-surface-900 dark:text-surface-100">L. {{ number_format((float) $register->total_cash, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-surface-600 dark:text-surface-400">Tarjeta</dt>
                        <dd class="font-medium text-surface-900 dark:text-surface-100">L. {{ number_format((float) $register->total_card, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-surface-600 dark:text-surface-400">Transferencia</dt>
                        <dd class="font-medium text-surface-900 dark:text-surface-100">L. {{ number_format((float) $register->total_transfer, 2) }}</dd>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-surface-200 dark:border-surface-800">
                        <dt class="font-bold text-surface-900 dark:text-surface-50">Total ventas</dt>
                        <dd class="font-bold text-brand-600 dark:text-brand-400">L. {{ number_format((float) $register->total_sales, 2) }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            <x-ui.card>
                <x-slot name="header">
                    <h2 class="font-semibold text-surface-900 dark:text-surface-100">Arqueo de efectivo</h2>
                </x-slot>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-surface-600 dark:text-surface-400">Monto inicial</dt>
                        <dd class="font-medium text-surface-900 dark:text-surface-100">L. {{ number_format((float) $register->opening_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-surface-600 dark:text-surface-400">+ Ventas en efectivo</dt>
                        <dd class="font-medium text-surface-900 dark:text-surface-100">L. {{ number_format((float) $register->total_cash, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-surface-600 dark:text-surface-400">= Esperado en caja</dt>
                        <dd class="font-bold text-surface-900 dark:text-surface-100">L. {{ number_format((float) $register->expected_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-surface-600 dark:text-surface-400">Monto real contado</dt>
                        <dd class="font-medium text-surface-900 dark:text-surface-100">L. {{ number_format((float) $register->actual_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-surface-200 dark:border-surface-800">
                        <dt class="font-bold text-surface-900 dark:text-surface-50">Diferencia</dt>
                        <dd class="font-bold text-lg {{ (float) $register->difference >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ (float) $register->difference >= 0 ? '+' : '' }}L. {{ number_format((float) $register->difference, 2) }}
                        </dd>
                    </div>
                </dl>
            </x-ui.card>

            @if ($register->notes)
                <x-ui.card>
                    <x-slot name="header">
                        <h2 class="font-semibold text-surface-900 dark:text-surface-100">Notas</h2>
                    </x-slot>
                    <p class="text-sm text-surface-700 dark:text-surface-300">{{ $register->notes }}</p>
                </x-ui.card>
            @endif
        </div>
    </div>
</div>
