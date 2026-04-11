@props([
    'icon' => null,
    'title' => 'No hay datos',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4']) }}>
    @if ($icon)
        <div class="mx-auto w-12 h-12 text-surface-400 mb-4">
            {{ $icon }}
        </div>
    @else
        <div class="mx-auto w-12 h-12 rounded-full bg-surface-100 dark:bg-surface-800 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        </div>
    @endif

    <h3 class="text-base font-semibold text-surface-900 dark:text-surface-100">{{ $title }}</h3>

    @if ($description)
        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400 max-w-sm mx-auto">{{ $description }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-6">
            {{ $slot }}
        </div>
    @endif
</div>
