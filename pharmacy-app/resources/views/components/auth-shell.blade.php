@props([
    'step' => '02',
    'eyebrow' => 'Iniciar sesión',
    'title' => 'Bienvenido de vuelta.',
    'description' => 'Ingresa tus credenciales para continuar.',
    'headline' => 'Gestión farmacéutica',
    'headlineItalic' => 'diseñada con precisión.',
    'subline' => 'Control de inventario, facturación transaccional y bitácora clínica en una sola plataforma. Auditable, segura, y con el rigor que una farmacia moderna necesita.',
])

<div class="flex min-h-screen flex-col md:flex-row">

    {{-- Panel visual editorial --}}
    <aside
        class="relative flex flex-col justify-between overflow-hidden bg-brand-900 px-8 py-10 text-brand-50 md:w-1/2 md:px-12 md:py-14 lg:w-[55%] lg:px-20 lg:py-20"
        aria-hidden="true"
    >
        <div class="absolute left-0 right-0 top-0 h-px bg-brand-700/60"></div>

        <div
            class="pointer-events-none absolute inset-0 opacity-[0.035]"
            style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 48px 48px;"
        ></div>

        <div class="relative animate-fade-in">
            <div class="flex items-start justify-between gap-6">
                <div class="flex items-center gap-3">
                    <svg viewBox="0 0 48 48" class="h-11 w-11 shrink-0" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Logo Hedman Garcia Pharmacy">
                        <g transform="rotate(-45 24 24)">
                            <rect x="6" y="18" width="36" height="12" rx="6" stroke="currentColor" stroke-width="1.5"/>
                            <line x1="24" y1="18" x2="24" y2="30" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="6" y="18" width="18" height="12" rx="6" fill="currentColor" fill-opacity="0.12"/>
                        </g>
                    </svg>
                    <div class="flex flex-col leading-tight">
                        <span class="font-['Fraunces'] text-lg font-medium tracking-tight text-brand-50">Hedman Garcia</span>
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em] text-brand-300">Pharmacy</span>
                    </div>
                </div>

                <span class="hidden shrink-0 font-mono text-[10px] uppercase tracking-[0.2em] text-brand-300 md:inline">
                    {{ $step }} — {{ $eyebrow }}
                </span>
            </div>
        </div>

        <div class="relative mt-10 max-w-xl animate-fade-in md:mt-0">
            <p class="mb-6 font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400">
                Sistema de gestión · v2
            </p>
            <h1 class="font-['Fraunces'] text-4xl font-[450] leading-[1.05] tracking-tight text-brand-50 md:text-5xl lg:text-[3.5rem]">
                {{ $headline }}<br>
                <em class="font-[450] italic text-brand-200">{{ $headlineItalic }}</em>
            </h1>
            <p class="mt-6 max-w-md text-sm leading-relaxed text-brand-200/80 md:text-base">
                {{ $subline }}
            </p>
        </div>

        <div class="relative mt-10 flex items-end justify-between gap-4 border-t border-brand-700/60 pt-6 font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 md:mt-0">
            <span>MMXXVI · Tegucigalpa, HN</span>
            <span class="hidden sm:inline">SSL · bcrypt · RBAC</span>
        </div>
    </aside>

    {{-- Panel formulario --}}
    <section class="flex flex-1 items-center justify-center bg-surface-50 px-6 py-12 dark:bg-surface-950 sm:px-10 md:w-1/2 lg:w-[45%] lg:px-16">
        <div class="w-full max-w-sm animate-fade-in">

            <header class="mb-10">
                <p class="mb-3 font-mono text-[10px] uppercase tracking-[0.2em] text-surface-400 dark:text-surface-500">
                    {{ $eyebrow }}
                </p>
                <h2 class="font-['Fraunces'] text-3xl font-[500] tracking-tight text-surface-900 dark:text-surface-50">
                    {{ $title }}
                </h2>
                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                    {{ $description }}
                </p>
            </header>

            {{ $slot }}

            <p class="mt-8 text-center font-mono text-[9px] uppercase tracking-[0.2em] text-surface-400 md:hidden dark:text-surface-600">
                MMXXVI · Tegucigalpa, HN
            </p>
        </div>
    </section>
</div>
