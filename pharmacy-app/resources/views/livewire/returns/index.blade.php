<div>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Devoluciones</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Historial de devoluciones procesadas.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('returns.flash'))
                <x-ui.alert variant="success" dismissible>{{ session('returns.flash') }}</x-ui.alert>
            @endif

            @if ($returns->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state title="Sin devoluciones" description="No se han procesado devoluciones." />
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Número</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Factura</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Fecha</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Procesado por</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Reembolso</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($returns as $return)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                <td class="px-4 py-3 font-mono text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $return->return_number }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-surface-600 dark:text-surface-400">{{ $return->invoice?->invoice_number ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $return->processed_at?->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $return->processedBy?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-surface-900 dark:text-surface-100">L. {{ number_format((float) $return->total_refund, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('returns.show', $return) }}"><x-ui.button variant="ghost" size="sm">Ver</x-ui.button></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
                <div class="mt-4">{{ $returns->links() }}</div>
            @endif
        </div>
    </div>
</div>
