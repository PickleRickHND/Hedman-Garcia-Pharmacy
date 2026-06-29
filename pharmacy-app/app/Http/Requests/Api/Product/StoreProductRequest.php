<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * La autorización por rol se aplica en la ruta (middleware role:Administrador);
     * aquí solo validamos el payload.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
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
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }
}
