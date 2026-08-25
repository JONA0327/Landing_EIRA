<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\Region;
use App\Models\Setting;
use App\Services\CrmCatalogClient;
use App\Services\GroqClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanelController extends Controller
{
    /** Entrada genérica del panel (ej. /panel-4life) — manda directo a la primera sección. */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.regiones');
    }

    public function regiones(): View
    {
        $regiones = Region::with('whatsappNumbers')->orderBy('orden')->get();

        return view('admin.regiones', compact('regiones'));
    }

    public function catalogo(GroqClient $groq, CrmCatalogClient $crm): View
    {
        $productos = CatalogProduct::with('regions')->orderBy('orden')->orderBy('id')->get();
        $regiones  = Region::orderBy('orden')->get();

        return view('admin.catalogo', [
            'productos'       => $productos,
            'regiones'        => $regiones,
            'crmConfigurado'  => $this->crmConfigurado(),
            'groqConfigurado' => $groq->configurado(),
        ]);
    }

    private function crmConfigurado(): bool
    {
        return (bool) Setting::get('crm_base_url', config('crm.base_url'))
            && (bool) Setting::get('crm_tenant_slug', config('crm.tenant_slug'))
            && (bool) Setting::get('crm_catalog_api_key', config('crm.catalog_api_key'))
            && (bool) Setting::get('crm_catalog_module', config('crm.catalog_module'));
    }

    /**
     * Campos editables desde el panel → clave en `settings` + su valor de
     * respaldo (.env) + si es secreto (se enmascara y nunca se pre-llena en
     * el formulario, para no exponerlo ni arriesgar un borrado accidental).
     */
    private function camposConfiguracion(): array
    {
        return [
            [
                'grupo' => 'CRM — Catálogo de productos', 'key' => 'crm_base_url', 'env' => 'CRM_BASE_URL',
                'label' => 'URL base del CRM', 'secreto' => false, 'placeholder' => 'https://tu-crm.com/api/v1',
                'valor' => Setting::get('crm_base_url', config('crm.base_url')),
            ],
            [
                'grupo' => 'CRM — Catálogo de productos', 'key' => 'crm_tenant_slug', 'env' => 'CRM_TENANT_SLUG',
                'label' => 'Slug del tenant', 'secreto' => false, 'placeholder' => 'mi-negocio',
                'valor' => Setting::get('crm_tenant_slug', config('crm.tenant_slug')),
            ],
            [
                'grupo' => 'CRM — Catálogo de productos', 'key' => 'crm_catalog_api_key', 'env' => 'CRM_CATALOG_API_KEY',
                'label' => 'API Key de catálogo (solo lectura)', 'secreto' => true, 'placeholder' => 'vit_...',
                'valor' => Setting::get('crm_catalog_api_key', config('crm.catalog_api_key')),
            ],
            [
                'grupo' => 'CRM — Catálogo de productos', 'key' => 'crm_catalog_module', 'env' => 'CRM_CATALOG_MODULE',
                'label' => 'Módulo del catálogo', 'secreto' => false, 'placeholder' => 'productos',
                'valor' => Setting::get('crm_catalog_module', config('crm.catalog_module')),
            ],
            [
                'grupo' => 'Groq — IA para descripciones simplificadas', 'key' => 'groq_api_key', 'env' => 'GROQ_API_KEY',
                'label' => 'API Key', 'secreto' => true, 'placeholder' => 'gsk_...',
                'valor' => Setting::get('groq_api_key', config('services.groq.key')),
            ],
            [
                'grupo' => 'Groq — IA para descripciones simplificadas', 'key' => 'groq_model', 'env' => 'GROQ_MODEL',
                'label' => 'Modelo', 'secreto' => false, 'placeholder' => 'openai/gpt-oss-20b',
                'valor' => Setting::get('groq_model', config('services.groq.model')),
            ],
        ];
    }

    /**
     * Panel — Configuración: permite editar CRM y Groq desde aquí mismo, sin
     * tocar el servidor. Se guarda en la tabla `settings`, que tiene
     * prioridad sobre el .env (ver App\Models\Setting) — así aplica al
     * instante, incluso con `config:cache` activo en producción.
     */
    public function configuracion(): View
    {
        $campos = collect($this->camposConfiguracion())->groupBy('grupo');

        // Ruta del panel y correo del admin quedan fuera de $campos a propósito:
        // cambiarlos aquí mismo (la ruta secreta de ESTE panel, o el email con
        // el que entras) es un riesgo real de auto-bloqueo, así que esos dos
        // se quedan de solo lectura, informativos, editables solo en el .env.
        $soloLectura = [
            ['env' => 'ADMIN_PANEL_PATH', 'label' => 'Ruta oculta del panel', 'valor' => config('panel.path')],
            ['env' => 'ADMIN_EMAIL',      'label' => 'Correo del admin',      'valor' => config('panel.admin_email')],
        ];

        return view('admin.configuracion', compact('campos', 'soloLectura'));
    }

    /**
     * Guarda los campos editables de Configuración en la tabla `settings`.
     *
     * Campos normales: lo que llegue manda tal cual (incluso vacío = borra el
     * override y vuelve a caer al .env) — el admin ve el valor actual en el
     * input, así que borrarlo es una acción consciente.
     *
     * Campos secretos: el input SIEMPRE llega vacío por diseño (nunca se
     * muestra la clave completa de vuelta) — vacío ahí significa "no lo
     * toques", para no borrar sin querer. Para borrar un secreto a propósito
     * hay que marcar su checkbox "Borrar".
     */
    public function guardarConfiguracion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'crm_base_url'        => ['nullable', 'string', 'max:255'],
            'crm_tenant_slug'     => ['nullable', 'string', 'max:100'],
            'crm_catalog_api_key' => ['nullable', 'string', 'max:255'],
            'crm_catalog_module'  => ['nullable', 'string', 'max:100'],
            'groq_api_key'        => ['nullable', 'string', 'max:255'],
            'groq_model'          => ['nullable', 'string', 'max:100'],
            'borrar'              => ['array'],
            'borrar.*'            => ['string'],
        ]);

        $borrar = $validated['borrar'] ?? [];
        $secretos = ['crm_catalog_api_key', 'groq_api_key'];

        foreach ($this->camposConfiguracion() as $campo) {
            $key = $campo['key'];

            if (in_array($key, $borrar, true)) {
                Setting::set($key, null);
                continue;
            }

            $valor = trim((string) ($validated[$key] ?? ''));

            if (in_array($key, $secretos, true) && $valor === '') {
                continue; // secreto sin tocar — no pisar lo que ya había
            }

            Setting::set($key, $valor !== '' ? $valor : null);
        }

        return back()->with('success', 'Configuración actualizada — ya está en uso.');
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
        $modulo = Setting::get('crm_catalog_module', config('crm.catalog_module'));

        if (! $modulo) {
            return back()->with('error', 'Falta el "Módulo del catálogo" — configúralo en la pestaña Configuración (el slug que ves en el sidebar "Catálogos" del CRM).');
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
