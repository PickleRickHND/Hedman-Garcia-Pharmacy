<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Nuevo producto')]
class Create extends Component
{
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
    public ?int $category_id = null;
    public ?int $supplier_id = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
    }

    protected function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:30', Rule::unique('products', 'sku')],
            'name' => ['required', 'string', 'min:2', 'max:'.config('pharmacy.limits.product_name_max')],
            'description' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_description_max')],
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:today'],
            'presentation' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_presentation_max')],
            'administration_form' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_administration_max')],
            'storage' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_storage_max')],
            'packaging' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_packaging_max')],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        Product::create($validated);

        session()->flash('products.flash', "Producto «{$validated['name']}» creado.");

        return redirect()->route('products.index');
    }

    public function render(): View
    {
        return view('livewire.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::active()->orderBy('name')->get(),
        ]);
    }
}
