<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'method',
        'amount',
        'cash_received',
        'change_amount',
        'status',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'midtrans_response',
        'va_number',
        'qris_url',
        'expires_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'cash_received' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'midtrans_response' => 'array',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function isCash(): bool
    {
        return $this->method === 'cash';
    }

    public function isQris(): bool
    {
        return $this->method === 'qris';
    }

    public function isVa(): bool
    {
        return $this->method === 'va';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function markAsSuccess(): void
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }
}
