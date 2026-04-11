<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-lg border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-card']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-800">
            {{ $slot }}
        </table>
    </div>
</div>
