@props(['code' => '404', 'title' => 'Página no encontrada', 'message' => 'La página que buscas no existe o fue movida.', 'link' => null, 'linkLabel' => 'Volver al inicio'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ $title }} · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:400,500|inter:400,500&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-surface-50 text-surface-900 dark:bg-surface-950 dark:text-surface-100">
    <main class="flex min-h-screen items-center justify-center px-6 py-16">
        <div class="max-w-lg text-center animate-fade-in">
            <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-700 dark:text-brand-400">
                <span class="inline-block h-1 w-8 bg-brand-600 align-middle"></span>
                Error {{ $code }}
            </p>
            <h1 class="mt-6 font-['Fraunces'] text-7xl font-[450] tracking-tight text-surface-900 dark:text-surface-50 sm:text-8xl">
                {{ $code }}
            </h1>
            <h2 class="mt-4 font-['Fraunces'] text-2xl font-[450] italic text-brand-700 dark:text-brand-400">
                {{ $title }}
            </h2>
            <p class="mt-6 text-sm leading-relaxed text-surface-600 dark:text-surface-400">
                {{ $message }}
            </p>
            <div class="mt-10">
                <a
                    href="{{ $link ?? url('/') }}"
                    class="group inline-flex items-center gap-2 bg-surface-900 px-6 py-3 text-sm font-medium tracking-wide text-surface-50 transition-all duration-200 hover:bg-brand-700 dark:bg-surface-50 dark:text-surface-900 dark:hover:bg-brand-400"
                >
                    <svg class="h-4 w-4 rotate-180 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    {{ $linkLabel }}
                </a>
            </div>
            <p class="mt-16 font-mono text-[9px] uppercase tracking-[0.2em] text-surface-400 dark:text-surface-600">
                Hedman Garcia Pharmacy · MMXXVI
            </p>
        </div>
    </main>
</body>
</html>
