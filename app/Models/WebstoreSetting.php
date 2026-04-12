<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebstoreSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public static function getByKey(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function setByKey(string $key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function isProtected(string $key): bool
    {
        return in_array($key, [
            'navigation_collection_groups',
            'home_featured_collections',
        ]);
    }

    protected static function booted()
    {
        static::deleting(function ($setting) {
            if (self::isProtected($setting->key)) {
                return false;
            }
        });
    }
}
