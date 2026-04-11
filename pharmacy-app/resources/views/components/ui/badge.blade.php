@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php
$variants = [
    'neutral' => 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-300',
    'brand'   => 'bg-brand-100 text-brand-800 dark:bg-brand-900/40 dark:text-brand-300',
    'success' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    'danger'  => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    'info'    => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-xs',
    'lg' => 'px-3 py-1.5 text-sm',
];

$classes = 'inline-flex items-center gap-1 font-medium rounded-full '
    . ($variants[$variant] ?? $variants['neutral']) . ' '
    . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
