<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Traits\BelongsToStore;

class Setting extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'store_id',
        'key',
        'value',
        'group',
    ];

    /**
     * Get the current store ID for scoping.
     */
    private static function currentStoreId(): ?int
    {
        return auth()->user()?->store_id;
    }

    /**
     * Get a setting value by key (scoped to current store).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $storeId = self::currentStoreId();
        if (!$storeId) return $default;

        $cacheKey = "setting.{$storeId}.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $storeId, $default) {
            $setting = self::withoutGlobalScope('store')
                ->where('store_id', $storeId)
                ->where('key', $key)
                ->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value (scoped to current store).
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        $storeId = self::currentStoreId();
        if (!$storeId) return;

        self::withoutGlobalScope('store')->updateOrCreate(
            ['store_id' => $storeId, 'key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting.{$storeId}.{$key}");
        Cache::forget("settings.group.{$storeId}.{$group}");
    }

    /**
     * Get all settings for a group (scoped to current store).
     */
    public static function getGroup(string $group): array
    {
        $storeId = self::currentStoreId();
        if (!$storeId) return [];

        return Cache::remember("settings.group.{$storeId}.{$group}", 3600, function () use ($group, $storeId) {
            return self::withoutGlobalScope('store')
                ->where('store_id', $storeId)
                ->where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Get tax rate from settings.
     */
    public static function getTaxRate(): float
    {
        return (float) self::get('tax_rate', 0);
    }

    /**
     * Check if tax is enabled.
     */
    public static function isTaxEnabled(): bool
    {
        return (bool) self::get('tax_enabled', false);
    }

    /**
     * Get store name.
     */
    public static function getStoreName(): string
    {
        return (string) self::get('store_name', 'POS Store');
    }

    /**
     * Create default settings for a new store (used during registration).
     */
    public static function createDefaults(int $storeId, string $storeName = 'My Store'): void
    {
        $defaults = [
            ['key' => 'store_name', 'value' => $storeName, 'group' => 'general'],
            ['key' => 'store_logo', 'value' => '', 'group' => 'general'],
            ['key' => 'tax_enabled', 'value' => '0', 'group' => 'tax'],
            ['key' => 'tax_rate', 'value' => '11', 'group' => 'tax'],
            ['key' => 'receipt_header', 'value' => '', 'group' => 'receipt'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih atas kunjungan Anda!', 'group' => 'receipt'],
        ];

        foreach ($defaults as $setting) {
            self::withoutGlobalScope('store')->create([
                'store_id' => $storeId,
                ...$setting,
            ]);
        }
    }
}
