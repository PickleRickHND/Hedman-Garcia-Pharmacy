<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' · '.config('app.name') : config('app.name', 'Hedman Garcia Pharmacy') }}</title>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,700|inter:400,500,600,700&display=swap" rel="stylesheet" />

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased selection:bg-brand-200 selection:text-brand-900 dark:selection:bg-brand-800 dark:selection:text-brand-50">

        {{-- Skip link --}}
        <a
            href="#main-content"
            class="sr-only focus-visible:not-sr-only focus-visible:fixed focus-visible:left-4 focus-visible:top-4 focus-visible:z-50 focus-visible:rounded-md focus-visible:bg-brand-600 focus-visible:px-4 focus-visible:py-2 focus-visible:text-sm focus-visible:font-medium focus-visible:text-white focus-visible:shadow-card"
        >
            Saltar al contenido
        </a>

        <div class="min-h-screen bg-surface-50 dark:bg-surface-950">
            <livewire:layout.navigation />

            @if (isset($header))
                <header class="border-b border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900">
                    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main id="main-content">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
