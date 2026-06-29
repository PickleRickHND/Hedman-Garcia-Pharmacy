<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\CashRegister;

use Illuminate\Foundation\Http\FormRequest;

class OpenCashRegisterRequest extends FormRequest
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
        return [
            'opening_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
