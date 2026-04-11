<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('users.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a usuarios
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Editar: {{ $user->name }}</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ $user->email }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="save">
                <x-ui.card>
                    <x-slot name="header">
                        <h2 class="font-semibold text-surface-900 dark:text-surface-100">Datos del usuario</h2>
                    </x-slot>

                    <div class="space-y-5">
                        <x-ui.input
                            label="Nombre completo"
                            name="name"
                            wire:model="name"
                            :error="$errors->first('name')"
                            required
                        />

                        <x-ui.input
                            label="Email"
                            name="email"
                            type="email"
                            wire:model="email"
                            :error="$errors->first('email')"
                            required
                        />

                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                                Rol <span class="text-danger">*</span>
                            </label>
                            <select
                                wire:model="role"
                                class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                            >
                                <option value="">Selecciona un rol</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="text-xs text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </x-ui.card>

                    <div class="mt-6">
                        <x-ui.card>
                            <x-slot name="header">
                                <h2 class="font-semibold text-surface-900 dark:text-surface-100">Cambiar contraseña</h2>
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">Opcional. Dejar en blanco para mantener la contraseña actual.</p>
                            </x-slot>

                            <div class="space-y-5">
                                <x-ui.input
                                    label="Nueva contraseña"
                                    name="password"
                                    type="password"
                                    wire:model="password"
                                    :error="$errors->first('password')"
                                    hint="Mínimo 8 caracteres"
                                />

                                <x-ui.input
                                    label="Confirmar contraseña"
                                    name="password_confirmation"
                                    type="password"
                                    wire:model="password_confirmation"
                                />
                            </div>

                            <x-slot name="footer">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('users.index') }}">
                                        <x-ui.button type="button" variant="secondary">Cancelar</x-ui.button>
                                    </a>
                                    <x-ui.button type="submit" variant="primary">
                                        Guardar cambios
                                    </x-ui.button>
                                </div>
                            </x-slot>
                        </x-ui.card>
                    </div>
            </form>
        </div>
    </div>
</div>
