<div>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Categorías</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                Gestión de categorías farmacéuticas para clasificar productos.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>{{ $flashMessage }}</x-ui.alert>
            @endif

            {{-- Formulario para crear --}}
            <x-ui.card>
                <x-slot name="header">
                    <h2 class="font-semibold text-surface-900 dark:text-surface-100">Nueva categoría</h2>
                </x-slot>

                <form wire:submit="save" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Nombre de la categoría"
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        />
                        @error('name')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex-1">
                        <input
                            type="text"
                            wire:model="description"
                            placeholder="Descripción (opcional)"
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        />
                        @error('description')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                    <div class="w-20 shrink-0">
                        <input
                            type="color"
                            wire:model="color"
                            class="h-[38px] w-full cursor-pointer rounded-md border border-surface-300 bg-white p-1 dark:border-surface-700 dark:bg-surface-800"
                            title="Color del badge"
                        />
                    </div>
                    <x-ui.button type="submit" variant="primary" class="shrink-0">Crear</x-ui.button>
                </form>
            </x-ui.card>

            {{-- Tabla de categorías --}}
            @if ($categories->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state
                        title="Sin categorías"
                        description="Crea la primera categoría con el formulario de arriba."
                    />
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Categoría</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Descripción</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Productos</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                                @if ($editingId === $category->id)
                                    {{-- Modo edición inline --}}
                                    <td class="px-4 py-3" colspan="3">
                                        <form wire:submit="update" class="flex flex-col sm:flex-row gap-2">
                                            <div class="w-14 shrink-0">
                                                <input type="color" wire:model="editColor" class="h-[34px] w-full cursor-pointer rounded border border-surface-300 bg-white p-0.5 dark:border-surface-700 dark:bg-surface-800" />
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" wire:model="editName" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" />
                                                @error('editName')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" wire:model="editDescription" placeholder="Descripción" class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100" />
                                            </div>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <x-ui.button type="submit" variant="primary" size="sm">Guardar</x-ui.button>
                                                <x-ui.button type="button" variant="ghost" size="sm" wire:click="cancelEdit">Cancelar</x-ui.button>
                                            </div>
                                        </form>
                                    </td>
                                    <td></td>
                                @else
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block h-3 w-3 rounded-full shrink-0" style="background-color: {{ $category->color }}"></span>
                                            <span class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400">{{ $category->description ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm font-semibold text-surface-900 dark:text-surface-100">{{ $category->products_count }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <x-ui.button variant="ghost" size="sm" wire:click="startEdit({{ $category->id }})">Editar</x-ui.button>
                                            <x-ui.button
                                                variant="ghost"
                                                size="sm"
                                                class="text-danger hover:bg-red-50 dark:hover:bg-red-950/30"
                                                wire:click="delete({{ $category->id }})"
                                                wire:confirm="¿Eliminar «{{ $category->name }}»? Los productos quedarán sin categoría."
                                            >
                                                Eliminar
                                            </x-ui.button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            @endif
        </div>
    </div>
</div>
