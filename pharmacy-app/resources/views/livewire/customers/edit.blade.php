<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('customers.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a clientes
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Editar: {{ $customer->name }}</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="save">
                <x-ui.card>
                    <x-slot name="header">
                        <h2 class="font-semibold text-surface-900 dark:text-surface-100">Datos del cliente</h2>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-ui.input label="Nombre" name="name" wire:model="name" :error="$errors->first('name')" required />
                        <x-ui.input label="RTN" name="rtn" wire:model="rtn" :error="$errors->first('rtn')" />
                        <x-ui.input label="Teléfono" name="phone" wire:model="phone" :error="$errors->first('phone')" />
                        <x-ui.input label="Email" name="email" type="email" wire:model="email" :error="$errors->first('email')" />
                    </div>

                    <div class="mt-5">
                        <x-ui.input label="Dirección" name="address" wire:model="address" :error="$errors->first('address')" />
                    </div>

                    <div class="mt-5">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Notas</label>
                        <textarea wire:model="notes" rows="2" class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"></textarea>
                    </div>

                    <x-slot name="footer">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('customers.index') }}"><x-ui.button type="button" variant="secondary">Cancelar</x-ui.button></a>
                            <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                        </div>
                    </x-slot>
                </x-ui.card>
            </form>
        </div>
    </div>
</div>
