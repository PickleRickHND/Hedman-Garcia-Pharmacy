<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'stock',
        'price',
        'expiration_date',
        'presentation',
        'administration_form',
        'storage',
        'packaging',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'price' => 'decimal:2',
            'expiration_date' => 'date',
        ];
    }

    // -----------------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------------

    public function scopeLowStock(Builder $query, ?int $threshold = null): Builder
    {
        $threshold ??= (int) config('pharmacy.stock.low_threshold', 10);

        return $query->where('stock', '<=', $threshold);
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock', 0);
    }

    public function scopeExpiringSoon(Builder $query, ?int $days = null): Builder
    {
        $days ??= (int) config('pharmacy.stock.expiring_soon_days', 30);

        return $query
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()]);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', now()->startOfDay());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
                ->orWhere('sku', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('presentation', 'like', $like);
        });
    }

    // -----------------------------------------------------------------------
    // Accessors
    // -----------------------------------------------------------------------

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= (int) config('pharmacy.stock.low_threshold', 10);
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock === 0;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date !== null && $this->expiration_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if ($this->expiration_date === null || $this->is_expired) {
            return false;
        }

        $days = (int) config('pharmacy.stock.expiring_soon_days', 30);

        return $this->expiration_date->diffInDays(now()) <= $days;
    }
}
