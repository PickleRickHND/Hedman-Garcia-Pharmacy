@props([
    'padding' => 'md',
    'hover' => false,
])

@php
$paddings = [
    'none' => '',
    'sm' => 'p-4',
    'md' => 'p-6',
    'lg' => 'p-8',
];

$classes = 'card ' . ($paddings[$padding] ?? $paddings['md']) . ($hover ? ' card-hover cursor-pointer' : '');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @isset($header)
        <div class="mb-4 pb-4 border-b border-surface-200 dark:border-surface-800">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-800">
            {{ $footer }}
        </div>
    @endisset
</div>
