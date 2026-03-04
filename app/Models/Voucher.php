<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToStore;

class Voucher extends Model
{
    use HasFactory, SoftDeletes, BelongsToStore;

    protected $fillable = [
        'store_id',
        'code',
        'discount_type',
        'discount_value',
        'min_transaction',
        'max_discount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_transaction' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                     ->where('valid_from', '<=', $now)
                     ->where('valid_until', '>=', $now)
                     ->where(function ($q) {
                         $q->whereNull('usage_limit')
                           ->orWhereColumn('used_count', '<', 'usage_limit');
                     });
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function isValid(): bool
    {
        $now = Carbon::now();

        return $this->is_active
            && $now->greaterThanOrEqualTo($this->valid_from)
            && $now->lessThanOrEqualTo($this->valid_until)
            && $this->hasRemainingUsage();
    }

    public function isUsableForAmount(float $amount): bool
    {
        return $this->isValid() && $amount >= $this->min_transaction;
    }

    public function hasRemainingUsage(): bool
    {
        if ($this->usage_limit === null) {
            return true;
        }

        return $this->used_count < $this->usage_limit;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            $discount = $subtotal * ($this->discount_value / 100);

            // Apply max discount cap if set
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }

            return $discount;
        }

        // Fixed discount
        return min((float) $this->discount_value, $subtotal);
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
