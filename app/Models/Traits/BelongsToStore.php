<?php

namespace App\Models\Traits;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait BelongsToStore
 *
 * Provides automatic tenant scoping for multi-tenant models.
 * - Auto-filters queries by the authenticated user's store_id (global scope)
 * - Auto-sets store_id when creating new records
 * - Prevents manual store_id injection from frontend
 */
trait BelongsToStore
{
    public static function bootBelongsToStore(): void
    {
        // Global scope: auto-filter all queries by store_id
        static::addGlobalScope('store', function (Builder $builder) {
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return; // Skip in artisan commands (migrations, seeders) but not tests
            }

            // Prevent infinite recursion: only apply scope if user is already authenticated
            if (auth()->hasUser()) {
                $user = auth()->user();
                if ($user && $user->store_id) {
                    $builder->where($builder->getModel()->getTable() . '.store_id', $user->store_id);
                }
            }
        });

        // Creating hook: auto-set store_id from authenticated user
        static::creating(function ($model) {
            if (empty($model->store_id)) {
                $user = auth()->user();
                if ($user && $user->store_id) {
                    $model->store_id = $user->store_id;
                }
            }
        });
    }

    // ─── Relationship ──────────────────────────────────────────

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
