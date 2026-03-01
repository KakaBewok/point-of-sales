<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function getTypeBadgeColor(): string
    {
        return match ($this->type) {
            'in', 'return' => 'green',
            'out', 'sale' => 'red',
            'adjustment' => 'yellow',
            default => 'gray',
        };
    }
}
