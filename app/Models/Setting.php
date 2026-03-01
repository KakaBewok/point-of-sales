<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return self::where('group', $group)
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
}
