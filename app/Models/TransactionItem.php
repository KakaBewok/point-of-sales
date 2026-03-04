<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToStore;

class TransactionItem extends Model
{
    use HasFactory, BelongsToStore;

    protected $fillable = [
        'store_id',
        'transaction_id',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'product_price' => 'decimal:2',
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Boot ───────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (TransactionItem $item) {
            $item->subtotal = $item->product_price * $item->quantity;
        });
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
