<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('users.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a usuarios
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Nuevo usuario</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="save">
                <x-ui.card>
                    <div class="space-y-5">
                        <x-ui.input
                            label="Nombre completo"
                            name="name"
                            wire:model="name"
                            :error="$errors->first('name')"
                            required
                            autofocus
                        />

                        <x-ui.input
                            label="Email"
                            name="email"
                            type="email"
                            wire:model="email"
                            :error="$errors->first('email')"
                            required
                        />

                        <x-ui.input
                            label="Contraseña"
                            name="password"
                            type="password"
                            wire:model="password"
                            :error="$errors->first('password')"
                            hint="Mínimo 8 caracteres"
                            required
                        />

                        <x-ui.input
                            label="Confirmar contraseña"
                            name="password_confirmation"
                            type="password"
                            wire:model="password_confirmation"
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

                        <label class="flex items-center gap-2 text-sm text-surface-700 dark:text-surface-300">
                            <input type="checkbox" wire:model="must_change_password" class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                            Forzar cambio de contraseña en el primer login
                        </label>
                    </div>

                    <x-slot name="footer">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('users.index') }}">
                                <x-ui.button type="button" variant="secondary">Cancelar</x-ui.button>
                            </a>
                            <x-ui.button type="submit" variant="primary">
                                Crear usuario
                            </x-ui.button>
                        </div>
                    </x-slot>
                </x-ui.card>
            </form>
        </div>
    </div>
</div>
