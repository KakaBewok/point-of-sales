<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToStore;

class ExpenseCategory extends Model
{
    use HasFactory, SoftDeletes, BelongsToStore;

    protected $fillable = [
        'store_id',
        'name',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
