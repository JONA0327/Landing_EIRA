<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Config editable desde el panel (Configuración) que tiene prioridad sobre
 * el .env — se guarda en base de datos a propósito, NO en config/*.php,
 * porque esos archivos se congelan con `php artisan config:cache` (usado en
 * producción vía deploy.sh) y un cambio hecho aquí nunca se reflejaría hasta
 * el siguiente deploy. Si no hay nada guardado para una clave, se usa el
 * valor de respaldo que le pases (normalmente el de config()/.env).
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        $valor = Cache::rememberForever(self::cacheKey($key), function () use ($key) {
            return static::query()->where('key', $key)->value('value');
        });

        return ($valor !== null && $valor !== '') ? $valor : $default;
    }

    public static function set(string $key, ?string $value): void
    {
        if ($value === null || $value === '') {
            static::where('key', $key)->delete();
        } else {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::cacheKey($key));
    }

    private static function cacheKey(string $key): string
    {
        return 'setting:' . $key;
    }
}
