<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public $timestamps = false;

    public const TYPE_SALE = 'sale';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_RETURN = 'return';
    public const TYPE_VOID = 'void';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_LOSS = 'loss';

    public const TYPES = [
        self::TYPE_SALE,
        self::TYPE_PURCHASE,
        self::TYPE_RETURN,
        self::TYPE_VOID,
        self::TYPE_ADJUSTMENT,
        self::TYPE_LOSS,
    ];

    public const TYPE_LABELS = [
        self::TYPE_SALE => 'Venta',
        self::TYPE_PURCHASE => 'Compra',
        self::TYPE_RETURN => 'Devolución',
        self::TYPE_VOID => 'Anulación',
        self::TYPE_ADJUSTMENT => 'Ajuste',
        self::TYPE_LOSS => 'Merma',
    ];

    public const TYPE_COLORS = [
        self::TYPE_SALE => 'danger',
        self::TYPE_PURCHASE => 'success',
        self::TYPE_RETURN => 'info',
        self::TYPE_VOID => 'warning',
        self::TYPE_ADJUSTMENT => 'neutral',
        self::TYPE_LOSS => 'danger',
    ];

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function getBadgeColorAttribute(): string
    {
        return self::TYPE_COLORS[$this->type] ?? 'neutral';
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
