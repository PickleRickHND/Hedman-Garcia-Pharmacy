<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'sku' => ['required', 'string', 'max:30', Rule::unique('products', 'sku')->ignore($productId)],
            'name' => ['required', 'string', 'min:2', 'max:'.config('pharmacy.limits.product_name_max')],
            'description' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_description_max')],
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            // En edición se permite fecha de vencimiento pasada (paridad con el componente Edit).
            'expiration_date' => ['nullable', 'date'],
            'presentation' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_presentation_max')],
            'administration_form' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_administration_max')],
            'storage' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_storage_max')],
            'packaging' => ['nullable', 'string', 'max:'.config('pharmacy.limits.product_packaging_max')],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
        ];
    }
}
