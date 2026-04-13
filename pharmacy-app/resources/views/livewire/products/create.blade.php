<div>
    <x-slot name="header">
        <div>
            <a href="{{ route('products.index') }}" class="text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver al inventario
            </a>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-50">Nuevo producto</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="save">
                <x-ui.card>
                    <x-slot name="header">
                        <h2 class="font-semibold text-surface-900 dark:text-surface-100">Información básica</h2>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <x-ui.input label="SKU" name="sku" wire:model="sku" :error="$errors->first('sku')" required />
                        <x-ui.input label="Nombre del producto" name="name" wire:model="name" :error="$errors->first('name')" required />
                    </div>

                    <div class="mt-5">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Categoría</label>
                        <select
                            wire:model="category_id"
                            class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        >
                            <option value="">Sin categoría</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-5">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Descripción</label>
                        <textarea
                            wire:model="description"
                            rows="3"
                            class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100"
                        ></textarea>
                        @error('description')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                </x-ui.card>

                <div class="mt-6">
                    <x-ui.card>
                        <x-slot name="header">
                            <h2 class="font-semibold text-surface-900 dark:text-surface-100">Stock y precio</h2>
                        </x-slot>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <x-ui.input label="Stock inicial" name="stock" type="number" min="0" wire:model="stock" :error="$errors->first('stock')" required />
                            <x-ui.input label="Precio (L.)" name="price" type="number" step="0.01" min="0" wire:model="price" :error="$errors->first('price')" required />
                            <x-ui.input label="Vencimiento" name="expiration_date" type="date" wire:model="expiration_date" :error="$errors->first('expiration_date')" />
                        </div>
                    </x-ui.card>
                </div>

                <div class="mt-6">
                    <x-ui.card>
                        <x-slot name="header">
                            <h2 class="font-semibold text-surface-900 dark:text-surface-100">Detalles farmacéuticos</h2>
                            <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">Opcionales</p>
                        </x-slot>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1.5">Proveedor</label>
                            <select wire:model="supplier_id" class="block w-full rounded-md border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-100">
                                <option value="">Sin proveedor</option>
                                @foreach ($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <x-ui.input label="Presentación" name="presentation" wire:model="presentation" hint="Ej: Tableta 500mg" />
                            <x-ui.input label="Forma de administración" name="administration_form" wire:model="administration_form" hint="Ej: Oral, Tópica, Inhalada" />
                            <x-ui.input label="Almacenamiento" name="storage" wire:model="storage" hint="Ej: Temperatura ambiente" />
                            <x-ui.input label="Empaque" name="packaging" wire:model="packaging" hint="Ej: Caja 20 und" />
                        </div>

                        <x-slot name="footer">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('products.index') }}">
                                    <x-ui.button type="button" variant="secondary">Cancelar</x-ui.button>
                                </a>
                                <x-ui.button type="submit" variant="primary">Crear producto</x-ui.button>
                            </div>
                        </x-slot>
                    </x-ui.card>
                </div>
            </form>
        </div>
    </div>
</div>
