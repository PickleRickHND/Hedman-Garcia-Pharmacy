<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-100">
            Gestion de Usuarios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-ui.card>
                <x-ui.empty-state
                    title="Modulo en construccion"
                    description="El modulo de gestion de usuarios estara disponible en la Fase 2 del rewrite."
                >
                    <x-ui.badge variant="brand">Fase 2</x-ui.badge>
                </x-ui.empty-state>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
