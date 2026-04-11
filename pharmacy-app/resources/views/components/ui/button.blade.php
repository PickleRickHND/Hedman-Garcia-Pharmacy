@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'icon' => null,
])

@php
$base = 'inline-flex items-center justify-center gap-2 font-medium rounded-md transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary'   => 'bg-brand-600 text-white hover:bg-brand-700 active:bg-brand-800 focus-visible:ring-brand-500 shadow-sm',
    'secondary' => 'bg-white text-surface-700 border border-surface-300 hover:bg-surface-50 active:bg-surface-100 focus-visible:ring-surface-400 shadow-sm dark:bg-surface-800 dark:text-surface-100 dark:border-surface-700 dark:hover:bg-surface-700',
    'danger'    => 'bg-danger text-white hover:bg-red-700 active:bg-red-800 focus-visible:ring-red-500 shadow-sm',
    'ghost'     => 'bg-transparent text-surface-700 hover:bg-surface-100 active:bg-surface-200 focus-visible:ring-surface-400 dark:text-surface-300 dark:hover:bg-surface-800',
    'link'      => 'bg-transparent text-brand-600 hover:text-brand-700 hover:underline focus-visible:ring-brand-500 dark:text-brand-400',
];

$sizes = [
    'xs' => 'px-2 py-1 text-xs',
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
    'xl' => 'px-6 py-3 text-base',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <span class="shrink-0">{{ $icon }}</span>
    @endif
    {{ $slot }}
</button>
