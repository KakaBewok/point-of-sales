<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Traits\BelongsToStore;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToStore;

    protected $fillable = [
        'store_id',
        'type',
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'cost_price',
        'stock',
        'low_stock_threshold',
        'image',
        'thumbnail',
        'is_active',
        'service_duration',
        'is_appointment_ready',
        'assigned_staff_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    // ─── Boot ───────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIsProduct($query)
    {
        return $query->where('type', 'product');
    }

    public function scopeIsService($query)
    {
        return $query->where('type', 'service');
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('type', 'service')
              ->orWhere(function ($subq) {
                  $subq->where('type', 'product')
                       ->where('stock', '>', 0);
              });
        });
    }

    public function scopeLowStock($query)
    {
        return $query->where('type', 'product')
                     ->whereColumn('stock', '<=', 'low_stock_threshold')
                     ->where('stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('type', 'product')
                     ->where('stock', '<=', 0);
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function isProductType(): bool
    {
        return $this->type === 'product';
    }

    public function isServiceType(): bool
    {
        return $this->type === 'service';
    }

    public function isLowStock(): bool
    {
        if ($this->isServiceType()) return false;
        return $this->stock <= $this->low_stock_threshold && $this->stock > 0;
    }

    public function isOutOfStock(): bool
    {
        if ($this->isServiceType()) return false;
        return $this->stock <= 0;
    }

    public function hasEnoughStock(int $quantity): bool
    {
        if ($this->isServiceType()) return true;
        return $this->stock >= $quantity;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
