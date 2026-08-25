<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Un país/región de venta (México, Colombia, Estados Unidos, Chile).
 * Cada uno tiene sus propios números de WhatsApp (uno por agente de ventas —
 * ver RegionWhatsappNumber), código de referido 4Life, tienda y dirección —
 * la landing pública los muestra según el país que elija el visitante en el
 * selector inicial.
 */
class Region extends Model
{
    protected $fillable = [
        'slug', 'nombre', 'bandera',
        'codigo_4life', 'tienda_url',
        'direccion', 'direccion_corta', 'lat', 'lng',
        'activo', 'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden'  => 'integer',
        'lat'    => 'float',
        'lng'    => 'float',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(CatalogProduct::class, 'catalog_product_region');
    }

    public function whatsappNumbers(): HasMany
    {
        return $this->hasMany(RegionWhatsappNumber::class);
    }

    /** Un número al azar entre los agentes de esta región, o null si no hay ninguno. */
    public function numeroWhatsappAleatorio(): ?string
    {
        $numeros = $this->whatsappNumbers()->pluck('numero');
        return $numeros->isNotEmpty() ? $numeros->random() : null;
    }

    /** Link de WhatsApp listo para usar (wa.me) con un agente al azar, o null si no hay ninguno configurado. */
    public function whatsappLink(?string $mensaje = null): ?string
    {
        $numero = $this->numeroWhatsappAleatorio();
        if (! $numero) {
            return null;
        }
        $url = 'https://wa.me/' . preg_replace('/\D+/', '', $numero);
        return $mensaje ? $url . '?text=' . rawurlencode($mensaje) : $url;
    }

    /**
     * Nombres de país (tal como los manda el campo "pais" del catálogo del
     * CRM, ej. "México", "Estados Unidos") → slugs de región conocidos.
     */
    private const ALIAS_PAIS = [
        'mexico'                    => 'mx',
        'mx'                        => 'mx',
        'colombia'                  => 'co',
        'co'                        => 'co',
        'chile'                     => 'cl',
        'cl'                        => 'cl',
        'estados unidos'            => 'us',
        'estados unidos de america' => 'us',
        'usa'                       => 'us',
        'eeuu'                      => 'us',
        'ee uu'                     => 'us',
        'united states'             => 'us',
        'united states of america'  => 'us',
        'us'                        => 'us',
    ];

    /**
     * Normaliza un nombre de país para compararlo contra ALIAS_PAIS: sin
     * acentos, minúsculas, sin puntos ("EE.UU." → "eeuu") y con los espacios
     * de sobra colapsados — para que "México", "mexico", "MÉXICO " o
     * "Estados Unidos." calcen igual.
     */
    private static function normalizarPais(string $nombre): string
    {
        $limpio = Str::of($nombre)->ascii()->lower()->toString();
        $limpio = preg_replace('/[^a-z\s]/', '', $limpio);
        return trim(preg_replace('/\s+/', ' ', $limpio));
    }

    /** Convierte una lista de nombres de país en los slugs de región que reconoce. */
    public static function slugsDesdeNombresPais(array $nombres): array
    {
        $slugs = [];
        foreach ($nombres as $nombre) {
            $normal = self::normalizarPais((string) $nombre);
            if (isset(self::ALIAS_PAIS[$normal])) {
                $slugs[] = self::ALIAS_PAIS[$normal];
            }
        }
        return array_values(array_unique($slugs));
    }

    /** Igual que slugsDesdeNombresPais() pero devuelve los IDs de Region correspondientes. */
    public static function idsDesdeNombresPais(array $nombres): array
    {
        $slugs = self::slugsDesdeNombresPais($nombres);
        if (empty($slugs)) {
            return [];
        }
        return self::whereIn('slug', $slugs)->pluck('id')->all();
    }
}
