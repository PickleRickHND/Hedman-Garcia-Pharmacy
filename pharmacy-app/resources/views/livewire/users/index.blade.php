<div>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Usuarios</h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Gestión de cuentas, roles y reset de contraseñas.
                </p>
            </div>
            <a href="{{ route('users.create') }}">
                <x-ui.button variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo usuario
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($flashMessage)
                <x-ui.alert :variant="$flashVariant ?? 'info'" dismissible>
                    {{ $flashMessage }}
                    @if ($generatedPassword)
                        <div class="mt-2 flex items-center gap-2 font-mono text-sm bg-white dark:bg-surface-900 px-3 py-2 rounded border border-surface-300 dark:border-surface-700">
                            <span class="text-surface-500 text-xs">Password temporal:</span>
                            <code class="text-surface-900 dark:text-surface-100 font-semibold">{{ $generatedPassword }}</code>
                        </div>
                    @endif
                </x-ui.alert>
            @endif

            {{-- Filtros --}}
            <x-ui.card padding="sm">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Buscar por nombre o email..."
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        />
                    </div>
                    <div class="sm:w-56">
                        <select
                            wire:model.live="roleFilter"
                            class="block w-full rounded-md border-surface-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        >
                            <option value="">Todos los roles</option>
                            @foreach ($this->roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-ui.card>

            {{-- Tabla --}}
            @if ($users->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state
                        title="Sin resultados"
                        description="Ajusta los filtros o crea un nuevo usuario."
                    >
                        <a href="{{ route('users.create') }}">
                            <x-ui.button variant="primary">Nuevo usuario</x-ui.button>
                        </a>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <x-ui.table>
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">
                                <button wire:click="sortBy('name')" class="flex items-center gap-1 hover:text-surface-900 dark:hover:text-surface-100">
                                    Nombre
                                    @if ($sortField === 'name')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">
                                <button wire:click="sortBy('email')" class="flex items-center gap-1 hover:text-surface-900 dark:hover:text-surface-100">
                                    Email
                                    @if ($sortField === 'email')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </button>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Rol</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-600 dark:text-surface-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800 bg-white dark:bg-surface-900">
                    @foreach ($users as $user)
                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-900/40">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 flex items-center justify-center text-sm font-semibold">
                                        {{ $user->initials }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $user->name }}</p>
                                        @if ($user->must_change_password)
                                            <p class="text-xs text-amber-600 dark:text-amber-400">Debe cambiar su contraseña</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-surface-600 dark:text-surface-400">{{ $user->email }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($user->primary_role)
                                    <x-ui.badge :variant="match($user->primary_role) {
                                        'Administrador' => 'brand',
                                        'Cajero' => 'info',
                                        default => 'neutral',
                                    }">
                                        {{ $user->primary_role }}
                                    </x-ui.badge>
                                @else
                                    <span class="text-xs text-surface-400">Sin rol</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('users.edit', $user) }}">
                                        <x-ui.button variant="ghost" size="sm">Editar</x-ui.button>
                                    </a>
                                    <x-ui.button
                                        variant="ghost"
                                        size="sm"
                                        wire:click="forceReset({{ $user->id }})"
                                        wire:confirm="¿Generar una nueva contraseña temporal para {{ $user->name }}?"
                                    >
                                        Reset
                                    </x-ui.button>
                                    @if ($user->id !== auth()->id())
                                        <x-ui.button
                                            variant="ghost"
                                            size="sm"
                                            class="text-danger hover:bg-red-50 dark:hover:bg-red-950/30"
                                            wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="¿Eliminar a {{ $user->name }}? Esta acción no se puede deshacer."
                                        >
                                            Eliminar
                                        </x-ui.button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </x-ui.table>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
