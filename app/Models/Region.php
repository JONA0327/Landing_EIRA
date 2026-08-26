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

    /** Cada cuántos segundos rota el agente "de turno" — 10 minutos. */
    private const VENTANA_ROTACION_SEGUNDOS = 600;

    /**
     * El agente (número + su propia tienda 4Life) que está "de turno" ahora
     * mismo. Rota cada 10 minutos de forma determinística — el mismo cálculo
     * da el mismo resultado para todos los visitantes durante esa ventana de
     * tiempo, sin necesitar guardar ningún estado ni cron — así el WhatsApp
     * y el botón "Comprar" apuntan SIEMPRE al mismo agente mientras dure su
     * turno, en vez de mandar cada clic a alguien distinto.
     */
    public function agenteActivo(): ?RegionWhatsappNumber
    {
        $agentes = $this->whatsappNumbers()->orderBy('id')->get();
        if ($agentes->isEmpty()) {
            return null;
        }

        $ventana = intdiv(time(), self::VENTANA_ROTACION_SEGUNDOS);
        $indice  = crc32($this->slug . '|' . $ventana) % $agentes->count();

        return $agentes[$indice];
    }

    /** Número del agente de turno, o null si esta región no tiene ninguno configurado. */
    public function numeroWhatsappActivo(): ?string
    {
        return $this->agenteActivo()?->numero;
    }

    /** Tienda del agente de turno — si no la puso, cae a la tienda general de la región. */
    public function tiendaActiva(): ?string
    {
        $agente = $this->agenteActivo();
        return $agente?->tienda_url ?: $this->tienda_url;
    }

    /** Código de referido del agente de turno — si no lo puso, cae al código general de la región. */
    public function codigoActivo(): ?string
    {
        $agente = $this->agenteActivo();
        return $agente?->codigo_4life ?: $this->codigo_4life;
    }

    /** Dirección corta (footer) del agente de turno — si no la puso, cae a la de la región. */
    public function direccionCortaActiva(): ?string
    {
        $agente = $this->agenteActivo();
        return $agente?->direccion_corta ?: $this->direccion_corta;
    }

    /** Link de WhatsApp listo para usar (wa.me) con el agente de turno, o null si no hay ninguno configurado. */
    public function whatsappLink(?string $mensaje = null): ?string
    {
        $numero = $this->numeroWhatsappActivo();
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
