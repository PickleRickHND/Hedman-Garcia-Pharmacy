<div>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Corte de caja</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Apertura, cierre y historial de cortes.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>{{ $flashMessage }}</x-ui.alert>
            @endif
            @if (session('cash.flash'))
                <x-ui.alert variant="success" dismissible>{{ session('cash.flash') }}</x-ui.alert>
            @endif

            {{-- Estado actual --}}
            @if ($currentOpen)
                <x-ui.card>
                    <x-slot name="header">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                            <h2 class="font-semibold text-surface-900 dark:text-surface-100">Caja abierta</h2>
                        </div>
                    </x-slot>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Abierta por</p>
                            <p class="font-medium text-surface-900 dark:text-surface-100">{{ $currentOpen->user?->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Desde</p>
                            <p class="font-medium text-surface-900 dark:text-surface-100">{{ $currentOpen->opened_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-surface-500 dark:text-surface-400">Monto inicial</p>
                            <p class="font-medium text-surface-900 dark:text-surface-100">L. {{ number_format((float) $currentOpen->opening_amount, 2) }}</p>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <a href="{{ route('cash-register.close', $currentOpen) }}">
                            <x-ui.button variant="primary">Cerrar caja</x-ui.button>
                        </a>
                    </x-slot>
                </x-ui.card>
            @else
                <x-ui.card>
                    <x-slot name="header">
                        <h2 class="font-semibold text-surface-900 dark:text-surface-100">Abrir caja</h2>
                    </x-slot>

                    <form wire:submit="openRegister" class="flex flex-col sm:flex-row gap-3 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Monto inicial en caja (L.)</label>
                            <input type="number" step="0.01" min="0" wire:model="openingAmount" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" />
                        </div>
                        <x-ui.button type="submit" variant="primary">Abrir caja</x-ui.button>
                    </form>
                </x-ui.card>
            @endif

            {{-- Historial --}}
            <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-50">Historial de cortes</h2>

            @if ($history->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state title="Sin cortes" description="No se han cerrado cajas todavía." />
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Fecha cierre</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Usuario</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Ventas</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Esperado</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Real</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Diferencia</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($history as $reg)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $reg->closed_at?->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $reg->user?->name }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">L. {{ number_format((float) $reg->total_sales, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-surface-600 dark:text-surface-400">L. {{ number_format((float) $reg->expected_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-surface-600 dark:text-surface-400">L. {{ number_format((float) $reg->actual_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold {{ (float) $reg->difference >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ (float) $reg->difference >= 0 ? '+' : '' }}L. {{ number_format((float) $reg->difference, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('cash-register.show', $reg) }}"><x-ui.button variant="ghost" size="sm">Ver</x-ui.button></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
                <div class="mt-4">{{ $history->links() }}</div>
            @endif
        </div>
    </div>
</div>
