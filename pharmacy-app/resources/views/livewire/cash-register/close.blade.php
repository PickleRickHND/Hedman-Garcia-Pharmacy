<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('cash-register.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a caja
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Cerrar caja</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                Abierta el {{ $register->opened_at->format('d M Y H:i') }} · Monto inicial: L. {{ number_format((float) $register->opening_amount, 2) }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>{{ $flashMessage }}</x-ui.alert>
            @endif

            <form wire:submit="close">
                <x-ui.card>
                    <x-slot name="header">
                        <h2 class="font-semibold text-surface-900 dark:text-surface-100">Conteo de efectivo</h2>
                    </x-slot>

                    <div class="space-y-4">
                        <x-ui.input label="Monto real contado (L.)" name="actualAmount" type="number" step="0.01" min="0" wire:model="actualAmount" :error="$errors->first('actualAmount')" required />

                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Notas (opcional)</label>
                            <textarea wire:model="notes" rows="3" class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" placeholder="Observaciones del cierre..."></textarea>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('cash-register.index') }}"><x-ui.button type="button" variant="secondary">Cancelar</x-ui.button></a>
                            <x-ui.button type="submit" variant="primary">Cerrar caja</x-ui.button>
                        </div>
                    </x-slot>
                </x-ui.card>
            </form>
        </div>
    </div>
</div>
