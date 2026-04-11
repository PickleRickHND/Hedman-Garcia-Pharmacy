@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'error' => null,
    'hint' => null,
    'required' => false,
])

@php
$id = $attributes->get('id') ?? $name;
$hasError = !empty($error);
$inputClasses = 'block w-full rounded-md border-surface-300 bg-white text-surface-900 shadow-sm transition-colors
    focus:border-brand-500 focus:ring-brand-500
    dark:bg-surface-900 dark:border-surface-700 dark:text-surface-100
    dark:focus:border-brand-500 dark:focus:ring-brand-500'
    . ($hasError ? ' border-danger focus:border-danger focus:ring-danger' : '');
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
            {{ $label }}
            @if ($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => $inputClasses]) }}
    />

    @if ($hint && !$hasError)
        <p class="text-xs text-surface-500 dark:text-surface-400">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p class="text-xs text-danger flex items-center gap-1" role="alert">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1zm0 8a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
