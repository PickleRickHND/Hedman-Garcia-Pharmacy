<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Código de recuperación de contraseña (OTP de un solo uso, hasheado).
 *
 * La tabla usa `created_at` con DEFAULT CURRENT_TIMESTAMP y no tiene
 * `updated_at`, por eso se deshabilitan los timestamps automáticos.
 */
class PasswordResetCode extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'code_hash',
        'attempts',
        'expires_at',
        'used_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
