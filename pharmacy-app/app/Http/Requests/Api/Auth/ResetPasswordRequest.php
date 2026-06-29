<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
        $length = (int) config('pharmacy.password_reset.code_length', 6);

        return [
            'email' => ['required', 'string', 'email', 'max:'.config('pharmacy.limits.user_email_max', 100)],
            'code' => ['required', 'string', 'digits:'.$length],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'code.required' => 'Ingresa el código que recibiste.',
            'code.digits' => 'El código debe tener :digits dígitos.',
            'password.required' => 'Ingresa la nueva contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ];
    }
}
