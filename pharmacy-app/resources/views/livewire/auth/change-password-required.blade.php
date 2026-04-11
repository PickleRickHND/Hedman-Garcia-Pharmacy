<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Cambio de contraseña requerido</h1>
        <p class="mt-2 text-sm text-surface-600 dark:text-surface-400">
            Un administrador generó una contraseña temporal para tu cuenta.
            Por seguridad, necesitas establecer una nueva contraseña antes de continuar.
        </p>
    </div>

    <form wire:submit="save" class="space-y-5">
        <x-ui.input
            label="Nueva contraseña"
            name="password"
            type="password"
            wire:model="password"
            :error="$errors->first('password')"
            hint="Mínimo 8 caracteres"
            required
            autofocus
        />

        <x-ui.input
            label="Confirmar contraseña"
            name="password_confirmation"
            type="password"
            wire:model="password_confirmation"
            required
        />

        <div class="pt-2">
            <x-ui.button type="submit" variant="primary" class="w-full">
                Actualizar contraseña
            </x-ui.button>
        </div>
    </form>
</div>
