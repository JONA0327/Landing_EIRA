<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\Region;
use App\Services\CrmCatalogClient;
use App\Services\GroqClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function index(GroqClient $groq): View
    {
        $productos = CatalogProduct::with('regions')->orderBy('orden')->orderBy('id')->get();
        $regiones  = Region::with('whatsappNumbers')->orderBy('orden')->get();

        return view('admin.dashboard', [
            'productos'      => $productos,
            'regiones'       => $regiones,
            'crmConfigurado'  => (bool) config('crm.base_url') && (bool) config('crm.tenant_slug') && (bool) config('crm.catalog_api_key') && (bool) config('crm.catalog_module'),
            'groqConfigurado' => $groq->configurado(),
        ]);
    }

    /** Nombres de campo posibles para el país/países en el catálogo del CRM. */
    private const CAMPOS_PAIS = ['pais', 'país', 'paises', 'países', 'countries', 'country'];

    /**
     * Groq (plan gratuito) tiene un límite de tokens por minuto — si hay
     * muchos productos sin descripción simplificada todavía (ej. la primera
     * vez que se usa esta función), generarlos TODOS de un jalón dispara
     * error 429. Se limita cuántos se generan por cada "Guardar cambios";
     * los que sobran quedan para el siguiente guardado (nada se pierde,
     * solo toma 2-3 clics en vez de uno).
     */
    private const MAX_GENERACIONES_POR_GUARDADO = 5;

    /**
     * Trae los registros actuales del catálogo configurado (CRM_CATALOG_MODULE)
     * y los guarda/actualiza localmente. Un producto se auto-marca según el
     * campo "pais" del catálogo (ej. ["Colombia","México"]) SOLO si todavía no
     * tiene ninguna región asignada — en cuanto el admin marca/desmarca algo
     * (aunque sea dejarlo en cero a propósito), un re-sync ya no lo vuelve a
     * tocar. Esto también aplica retroactivo a productos sincronizados antes
     * de que existiera esta detección automática.
     */
    public function sync(CrmCatalogClient $crm): RedirectResponse
    {
        $modulo = config('crm.catalog_module');

        if (! $modulo) {
            return back()->with('error', 'Falta CRM_CATALOG_MODULE en el .env — dime el slug del catálogo (el que ves en el sidebar "Catálogos" del CRM) y lo agrego.');
        }

        $resultado = $crm->catalogo($modulo);

        if (! $resultado['success']) {
            return back()->with('error', 'No se pudo sincronizar: ' . ($resultado['error'] ?? 'error desconocido.'));
        }

        $vistos = 0;
        $nuevosConPais = 0;
        foreach ($resultado['data'] as $registro) {
            if (! isset($registro['id'])) {
                continue;
            }

            $datos = $registro['datos'] ?? $registro;

            $producto = CatalogProduct::updateOrCreate(
                ['crm_record_id' => $registro['id']],
                ['datos' => $datos, 'synced_at' => now()]
            );

            if ($producto->regions()->count() === 0) {
                $regionIds = Region::idsDesdeNombresPais($this->extraerPaises($datos));
                if (! empty($regionIds)) {
                    $producto->regions()->sync($regionIds);
                    $nuevosConPais++;
                }
            }

            $vistos++;
        }

        $mensaje = "Sincronizado: {$vistos} producto(s) traído(s) del catálogo '{$modulo}'.";
        if ($nuevosConPais > 0) {
            $mensaje .= " {$nuevosConPais} nuevo(s) con país(es) detectado(s) automáticamente del catálogo.";
        }

        return back()->with('success', $mensaje);
    }

    /** Lee el campo país/países de $datos (probando varios nombres de campo posibles) como array de strings. */
    private function extraerPaises(array $datos): array
    {
        foreach (self::CAMPOS_PAIS as $campo) {
            if (! isset($datos[$campo])) {
                continue;
            }
            $valor = $datos[$campo];
            return is_array($valor) ? $valor : [(string) $valor];
        }
        return [];
    }

    /**
     * Guarda, por producto, en qué región(es) se muestra, en qué orden, y su
     * descripción simplificada.
     *
     * La descripción simplificada NUNCA se genera de más: si el admin escribió
     * algo en el textarea (aunque sea lo mismo que ya había), eso es lo que
     * manda; si el campo llega vacío y el producto todavía no tiene una
     * descripción simplificada guardada, AHÍ se genera una vez con Groq y
     * queda cacheada — un siguiente "Guardar cambios" sin tocar ese campo no
     * vuelve a llamar a la IA.
     *
     * Body: { regiones: {producto_id: [region_id,...]}, orden: {producto_id: n},
     *         descripcion_simple: {producto_id: "texto"} }
     */
    public function updateProductRegions(Request $request, GroqClient $groq): RedirectResponse
    {
        $validated = $request->validate([
            'regiones'             => ['array'],
            'regiones.*'           => ['array'],
            'regiones.*.*'         => ['integer', 'exists:regions,id'],
            'orden'                => ['array'],
            'descripcion_simple'   => ['array'],
            'descripcion_simple.*' => ['nullable', 'string', 'max:2000'],
        ]);

        $regionesPorProducto = $validated['regiones'] ?? [];
        $orden               = $validated['orden'] ?? [];
        $descripcionesManual = $validated['descripcion_simple'] ?? [];

        $generadas = 0;
        $pendientes = 0;
        foreach (CatalogProduct::all() as $producto) {
            $producto->orden = (int) ($orden[$producto->id] ?? $producto->orden);

            $manual = trim((string) ($descripcionesManual[$producto->id] ?? ''));
            if ($manual !== '') {
                $producto->descripcion_simple = $manual;
            } elseif (empty($producto->descripcion_simple) && $producto->descripcion) {
                if ($generadas >= self::MAX_GENERACIONES_POR_GUARDADO) {
                    $pendientes++;
                } else {
                    $simple = $groq->simplificarDescripcion($producto->nombre, $producto->descripcion);
                    if ($simple) {
                        $producto->descripcion_simple = $simple;
                        $producto->descripcion_simple_generada_en = now();
                        $generadas++;
                    } else {
                        $pendientes++;
                    }
                }
            }

            $producto->save();
            $producto->regions()->sync($regionesPorProducto[$producto->id] ?? []);
        }

        $mensaje = 'Cambios guardados — ya se reflejan en la landing.';
        if ($generadas > 0) {
            $mensaje .= " Se generaron {$generadas} descripción(es) simplificada(s) con IA.";
        }
        if ($pendientes > 0) {
            $mensaje .= " Quedan {$pendientes} sin generar (límite de la API o pendientes) — vuelve a pulsar \"Guardar cambios\" para seguir.";
        } elseif ($generadas === 0 && ! $groq->configurado()) {
            $mensaje .= ' (GROQ_API_KEY no está configurado — las descripciones simplificadas no se generan automáticamente, pero puedes escribirlas a mano.)';
        }

        return back()->with('success', $mensaje);
    }
}
