<div class="relative" wire:poll.60s>
    <button
        wire:click="toggle"
        class="relative inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
        aria-label="Notificaciones"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if ($this->totalCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                {{ $this->totalCount > 99 ? '99+' : $this->totalCount }}
            </span>
        @endif
    </button>

    @if ($open)
        <div
            class="absolute right-0 z-50 mt-2 w-72 rounded-lg border border-surface-200 bg-white shadow-lg dark:border-surface-700 dark:bg-surface-800"
            wire:click.outside="toggle"
        >
            <div class="px-4 py-3 border-b border-surface-200 dark:border-surface-700">
                <p class="text-sm font-semibold text-surface-900 dark:text-surface-100">Alertas de inventario</p>
            </div>

            @if (count($this->alerts) === 0)
                <div class="px-4 py-6 text-center text-sm text-surface-500 dark:text-surface-400">
                    Sin alertas activas
                </div>
            @else
                <div class="divide-y divide-surface-200 dark:divide-surface-700 max-h-64 overflow-y-auto">
                    @foreach ($this->alerts as $alert)
                        <a
                            href="{{ $alert['route'] }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition"
                        >
                            <span class="shrink-0">
                                @if ($alert['variant'] === 'danger')
                                    <span class="flex h-2 w-2 rounded-full bg-red-500"></span>
                                @else
                                    <span class="flex h-2 w-2 rounded-full bg-amber-500"></span>
                                @endif
                            </span>
                            <span class="text-sm text-surface-700 dark:text-surface-300">{{ $alert['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
