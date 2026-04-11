<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Editar producto')]
class Edit extends Component
{
    public Product $product;
    public string $sku = '';
    public string $name = '';
    public string $description = '';
    public int $stock = 0;
    public string $price = '';
    public ?string $expiration_date = null;
    public string $presentation = '';
    public string $administration_form = '';
    public string $storage = '';
    public string $packaging = '';

    public function mount(Product $product): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);

        $this->product = $product;
        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->stock = $product->stock;
        $this->price = (string) $product->price;
        $this->expiration_date = $product->expiration_date?->format('Y-m-d');
        $this->presentation = $product->presentation ?? '';
        $this->administration_form = $product->administration_form ?? '';
        $this->storage = $product->storage ?? '';
        $this->packaging = $product->packaging ?? '';
    }

    protected function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:30', Rule::unique('products', 'sku')->ignore($this->product->id)],
            'name' => ['required', 'string', 'min:2', 'max:'.config('pharmacy.limits.product_name_max')],
            'description' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_description_max')],
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'expiration_date' => ['nullable', 'date'],
            'presentation' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_presentation_max')],
            'administration_form' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_administration_max')],
            'storage' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_storage_max')],
            'packaging' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_packaging_max')],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $this->product->update($validated);

        session()->flash('products.flash', "Producto actualizado.");

        return redirect()->route('products.index');
    }

    public function render(): View
    {
        return view('livewire.products.edit');
    }
}
