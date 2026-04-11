<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    public const STATUS_EMITTED = 'emitted';
    public const STATUS_VOIDED = 'voided';

    protected $fillable = [
        'invoice_number',
        'user_id',
        'payment_method_id',
        'customer_name',
        'customer_rtn',
        'subtotal',
        'tax',
        'total',
        'status',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'issued_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function scopeForToday($query)
    {
        return $query->whereDate('issued_at', today());
    }

    public function scopeEmitted($query)
    {
        return $query->where('status', self::STATUS_EMITTED);
    }
}
