<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Hedman Garcia Pharmacy') }}</title>

        {{-- Fonts: Inter (body) + Fraunces (display serif) --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,700,900|inter:400,500,600,700&display=swap" rel="stylesheet" />

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased bg-surface-50 text-surface-900 dark:bg-surface-950 dark:text-surface-100 selection:bg-brand-200 selection:text-brand-900 dark:selection:bg-brand-800 dark:selection:text-brand-50">
        <a
            href="#main-content"
            class="sr-only focus-visible:not-sr-only focus-visible:fixed focus-visible:left-4 focus-visible:top-4 focus-visible:z-50 focus-visible:rounded-md focus-visible:bg-brand-600 focus-visible:px-4 focus-visible:py-2 focus-visible:text-sm focus-visible:font-medium focus-visible:text-white focus-visible:shadow-card"
        >
            Saltar al contenido
        </a>

        <main id="main-content" class="min-h-screen">
            {{ $slot }}
        </main>
    </body>
</html>
