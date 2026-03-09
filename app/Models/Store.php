<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'subscription_status',
        'trial_ends_at',
        'qris_image_path',
        'qris_payload',
        'subscription_plan',
        'subscription_starts_at',
        'subscription_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'subscription_starts_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Relationships ─────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->subscription_status === 'active';
    }

    public function isTrial(): bool
    {
        return $this->subscription_status === 'trial';
    }

    public function isSuspended(): bool
    {
        return $this->subscription_status === 'suspended';
    }

    public function isExpired(): bool
    {
        return $this->subscription_status === 'expired';
    }

    public function isTrialExpired(): bool
    {
        return $this->isTrial() && $this->trial_ends_at?->isPast();
    }

    // Update canAccess logic according to new business rules
    public function canAccess(): bool
    {
        if ($this->isExpired() || $this->isSuspended() || $this->subscription_status === 'cancelled') {
            return false;
        }

        if ($this->isTrial() && !$this->isTrialExpired()) return true;

        if ($this->isActive() && $this->subscription_ends_at && !$this->subscription_ends_at->isPast()) {
            return true;
        }

        return false;
    }
}
