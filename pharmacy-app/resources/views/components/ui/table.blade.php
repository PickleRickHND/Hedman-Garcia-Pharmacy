@props([
    'headers' => [],
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-lg border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-card']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-800">
            @if (count($headers) > 0)
                <thead class="bg-surface-50 dark:bg-surface-900/50">
                    <tr>
                        @foreach ($headers as $header)
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
