<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use App\Traits\BelongsToTenant;

#[Fillable(['key', 'value', 'type', 'group'])]
class Setting extends Model
{
    use BelongsToTenant;

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $tenantId = auth()->check() ? auth()->user()->tenant_id : (session('tenant_id') ?? 1);
        $cached = Cache::rememberForever("sys_setting.t{$tenantId}.{$key}", function () use ($key) {
            $setting = self::where('key', $key)->first();
            if (!$setting) {
                return null;
            }
            // Store only plain primitives — never a Model object
            return ['value' => $setting->value, 'type' => $setting->type];
        });

        // Guard: if the cache returned a corrupted/incomplete object, fetch fresh from DB
        if (!$cached || !is_array($cached)) {
            Cache::forget("sys_setting.t{$tenantId}.{$key}");
            $setting = self::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }
            return self::castValue($setting->value, $setting->type);
        }

        return self::castValue($cached['value'], $cached['type']);
    }

    /**
     * Set/update a setting value.
     */
    public static function set(string $key, mixed $value, ?string $type = null, ?string $group = null): self
    {
        $type = $type ?? self::detectType($value);
        $rawValue = self::serializeValue($value, $type);

        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $rawValue,
                'type' => $type,
                'group' => $group ?? 'general',
            ]
        );

        $tenantId = auth()->check() ? auth()->user()->tenant_id : (session('tenant_id') ?? 1);
        Cache::forget("sys_setting.t{$tenantId}.{$key}");
        Cache::forget("all_settings");

        return $setting;
    }

    /**
     * Check if key exists.
     */
    public static function has(string $key): bool
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Cast raw database value to PHP type.
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $value,
            'float', 'double' => (float) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Serialize PHP value to raw string.
     */
    protected static function serializeValue(mixed $value, string $type): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            'array', 'json' => json_encode($value),
            'boolean', 'bool' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    /**
     * Detect type from value.
     */
    protected static function detectType(mixed $value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_float($value)) {
            return 'float';
        }
        if (is_array($value)) {
            return 'array';
        }
        return 'string';
    }
}
