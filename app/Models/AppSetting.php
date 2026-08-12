<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'encrypted'];

    protected function casts(): array
    {
        return ['encrypted' => 'boolean'];
    }

    public static function valueFor(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting || $setting->value === null) {
            return $default;
        }

        return $setting->encrypted ? Crypt::decryptString($setting->value) : $setting->value;
    }

    public static function put(string $key, mixed $value, bool $encrypted = false): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $encrypted && filled($value) ? Crypt::encryptString((string) $value) : $value, 'encrypted' => $encrypted],
        );
    }
}
