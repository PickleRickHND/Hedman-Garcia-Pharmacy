<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        // Limpia el archivo de imagen solo en borrado definitivo (un restore lo necesitaría).
        static::forceDeleted(function (Product $product): void {
            if (filled($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
        });
    }

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
        'image_path',
        'category_id',
        'supplier_id',
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
    // Relationships
    // -----------------------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // -----------------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------------

    public function scopeByCategory(Builder $query, ?int $categoryId): Builder
    {
        if ($categoryId === null) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

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

    /**
     * URL publica de la imagen del producto, o null si no tiene.
     * Usa el disco 'public' (requiere `php artisan storage:link`).
     */
    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image_path)) {
            return null;
        }

        return asset('storage/'.$this->image_path);
    }

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
