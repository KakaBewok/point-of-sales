<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToStore;

class StockLog extends Model
{
    use HasFactory, BelongsToStore;

    protected $fillable = [
        'store_id',
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            'sale' => 'Sale',
            'return' => 'Return',
            default => ucfirst($this->type),
        };
    }

    public function getBadgeColor(): string
    {
        return match ($this->type) {
            'in', 'return' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
            'out', 'sale' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'adjustment' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            default => 'bg-zinc-50 text-zinc-600 dark:bg-zinc-800/50 dark:text-zinc-400',
        };
    }
}
