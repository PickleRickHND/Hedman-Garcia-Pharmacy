<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'min:2', 'max:'.config('pharmacy.limits.user_name_max')],
            'email' => ['required', 'email', 'max:'.config('pharmacy.limits.user_email_max'), Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', Rule::in(Role::pluck('name')->toArray())],
            // Password opcional en edición: solo se cambia si viene en el payload.
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}
