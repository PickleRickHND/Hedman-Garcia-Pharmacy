<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnOrder extends Model
{
    protected $table = 'returns';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'return_number',
        'invoice_id',
        'user_id',
        'reason',
        'total_refund',
        'status',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_refund' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}
