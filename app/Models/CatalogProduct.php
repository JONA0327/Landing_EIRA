<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Copia local (cache) de un registro del catálogo del CRM. Un producto es
 * "visible" en una región si tiene esa Region asociada (tabla pivote
 * catalog_product_region) — sin regiones asociadas no aparece en ningún
 * lado. La landing NUNCA llama al CRM en vivo por cada visitante — solo el
 * panel admin sincroniza al pulsar "Sincronizar con CRM", y la landing lee
 * esta tabla local.
 */
class CatalogProduct extends Model
{
    protected $fillable = [
        'crm_record_id', 'datos', 'orden', 'synced_at',
        'descripcion_simple', 'descripcion_simple_generada_en',
    ];

    protected $casts = [
        'datos'                          => 'array',
        'orden'                          => 'integer',
        'synced_at'                      => 'datetime',
        'descripcion_simple_generada_en' => 'datetime',
    ];

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'catalog_product_region');
    }

    /**
     * Los slugs de campo del catálogo del CRM no se conocen de antemano —
     * se intentan varios nombres comunes en español/inglés hasta encontrar uno.
     */
    private function primero(array $candidatos): ?string
    {
        $datos = $this->datos ?? [];
        foreach ($candidatos as $clave) {
            if (! isset($datos[$clave])) {
                continue;
            }

            $valor = $datos[$clave];

            // Algunos campos del CRM (multi-selección, varias imágenes) llegan
            // como array — sin esto, (string) $array revienta con "Array to
            // string conversion". Tomamos el primer elemento no vacío.
            if (is_array($valor)) {
                $valor = collect($valor)->first(fn ($v) => ! is_array($v) && trim((string) $v) !== '');
            }

            if ($valor !== null && trim((string) $valor) !== '') {
                return (string) $valor;
            }
        }
        return null;
    }

    /**
     * En este CRM "producto" no es un nombre suelto: es un objeto
     * {"categoria": "...", "items": ["4Life RioVida Burst"]} — el nombre real
     * del producto vive en items[0], y "categoria" es la línea/familia a la
     * que pertenece (se muestra aparte, como etiqueta).
     */
    private function productoRaw(): array
    {
        $valor = ($this->datos ?? [])['producto'] ?? null;
        return is_array($valor) ? $valor : [];
    }

    public function getNombreAttribute(): string
    {
        $items = $this->productoRaw()['items'] ?? null;
        if (is_array($items)) {
            $item = collect($items)->first(fn ($v) => ! is_array($v) && trim((string) $v) !== '');
            if ($item !== null) {
                return trim((string) $item);
            }
        }

        return $this->primero(['nombre', 'name', 'titulo', 'title']) ?? ('Producto #' . $this->crm_record_id);
    }

    /** La categoría/línea del producto (ej. "Sistema Inmunológico") — se muestra como etiqueta, no como título. */
    public function getCategoriaAttribute(): ?string
    {
        $categoria = $this->productoRaw()['categoria'] ?? null;
        if (is_string($categoria) && trim($categoria) !== '') {
            return trim($categoria);
        }

        return $this->primero(['categoria', 'category', 'linea', 'línea']);
    }

    public function getDescripcionAttribute(): ?string
    {
        return $this->primero(['descripcion', 'description', 'detalle', 'resumen']);
    }

    public function getPrecioAttribute(): ?string
    {
        return $this->primero(['precio', 'price', 'precio_mxn', 'costo']);
    }

    public function getImagenAttribute(): ?string
    {
        $valor = $this->primero(['imagen', 'imagen_url', 'foto', 'image', 'image_url', 'foto_url']);
        if (! $valor) {
            return null;
        }
        if (str_starts_with($valor, 'http://') || str_starts_with($valor, 'https://')) {
            return $valor;
        }

        // Rutas relativas (ej. "catalog/xyz.jpg") vienen del storage público del
        // CRM, que vive en la raíz del dominio, no bajo /api/v1.
        $raiz = preg_replace('#/api/v1/?$#', '', (string) Setting::get('crm_base_url', config('crm.base_url')));
        return rtrim($raiz, '/') . '/storage/' . ltrim($valor, '/');
    }

    /**
     * Lo que se muestra en la tarjeta de la landing: la versión simplificada
     * (generada con IA o editada a mano en el panel) si ya existe; si todavía
     * no se ha generado (producto recién sincronizado, admin no ha guardado
     * cambios aún), cae a un recorte corto de la descripción original para
     * que la tarjeta no se vea vacía mientras tanto.
     */
    public function getDescripcionLandingAttribute(): ?string
    {
        if (! empty($this->descripcion_simple)) {
            return $this->descripcion_simple;
        }
        return $this->descripcion ? \Illuminate\Support\Str::limit($this->descripcion, 140) : null;
    }
}
