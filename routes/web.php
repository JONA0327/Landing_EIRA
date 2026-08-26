<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\RegionController;
use App\Models\CatalogProduct;
use App\Models\Region;
use App\Services\IpGeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request, IpGeoService $ipGeo) {
    // Productos que el admin activó para al menos una región, con sus
    // regiones cargadas para que el JS de la landing pueda filtrar por país
    // sin volver a pedirle nada al servidor. Lee la copia local
    // (catalog_products) — nunca llama al CRM en vivo por cada visitante.
    $productosDinamicos = CatalogProduct::has('regions')
        ->with('regions:id,slug')
        ->orderBy('orden')
        ->orderBy('id')
        ->get();

    $regiones = Region::where('activo', true)->with('whatsappNumbers')->orderBy('orden')->get();

    // País sugerido por IP (respeta VPN/proxy, no pide permiso, no depende de
    // APIs del navegador) — solo se usa como sugerencia inicial si el
    // visitante no tiene ya un país guardado de una visita anterior; null si
    // no se pudo determinar o no es uno de los países que manejamos.
    $paisPorIp = $ipGeo->paisPorIp($request->ip());
    $ipDetectadaSlug = ($paisPorIp && $regiones->contains('slug', $paisPorIp)) ? $paisPorIp : null;

    // Datos de cada región listos para el JS de la landing (selector de país,
    // WhatsApp/tienda/código dinámicos) — evita construir el array dentro del
    // Blade. "whatsapp", "tiendaUrl", "codigo4life" y "direccionCorta" son
    // del agente que está DE TURNO ahora mismo (rota cada 10 min, ver
    // Region::agenteActivo()) — así el mismo agente recibe el WhatsApp, el
    // clic de "Comprar" y el código, todo junto, mientras dure su turno, en
    // vez de repartir cada uno a alguien distinto. Si el agente no puso su
    // propia ciudad, cae a la general de la región.
    $regionesJs = $regiones->mapWithKeys(function ($r) {
        $agente = $r->agenteActivo();
        return [$r->slug => [
            'nombre'         => $r->nombre,
            'bandera'        => $r->bandera,
            'whatsapp'       => $agente?->numero,
            'direccionCorta' => $agente?->direccion_corta ?: $r->direccion_corta,
            'tiendaUrl'      => $agente?->tienda_url ?: $r->tienda_url,
            'codigo4life'    => $agente?->codigo_4life ?: $r->codigo_4life,
        ]];
    });

    // JSON_INVALID_UTF8_SUBSTITUTE + el fallback '{}' evitan que un campo con
    // bytes UTF-8 corruptos (typo, copy-paste raro) tumbe json_encode() y deje
    // "const REGIONS = ;" — un error de JS que rompería TODA la landing.
    $regionesJson = json_encode($regionesJs, JSON_INVALID_UTF8_SUBSTITUTE) ?: '{}';

    return view('welcome', compact('productosDinamicos', 'regiones', 'regionesJson', 'ipDetectadaSlug'));
});

// ── Panel admin oculto ───────────────────────────────────────────────────
// No hay ningún link a esto desde la landing pública — solo quien conoce
// esta URL (config('panel.path'), por defecto "panel-4life") puede llegar
// aquí. La ruta de login se llama exactamente 'login' (sin prefijo en el
// nombre) porque el middleware `auth` de Laravel redirige con route('login')
// a secas — con otro nombre de ruta, el redirect automático no funcionaría.
Route::prefix(config('panel.path'))->middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::prefix(config('panel.path'))->name('admin.')->middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // "/" manda directo a Regiones — admin.dashboard se conserva como alias
    // por si alguien tiene la URL base guardada.
    Route::get('/', [PanelController::class, 'index'])->name('dashboard');

    Route::get('/regiones', [PanelController::class, 'regiones'])->name('regiones');
    Route::put('/regiones/{region}', [RegionController::class, 'update'])->name('regiones.update');

    Route::get('/catalogo', [PanelController::class, 'catalogo'])->name('catalogo');
    Route::post('/sync', [PanelController::class, 'sync'])->name('sync');
    Route::post('/productos-regiones', [PanelController::class, 'updateProductRegions'])->name('productos-regiones');

    Route::get('/configuracion', [PanelController::class, 'configuracion'])->name('configuracion');
    Route::post('/configuracion', [PanelController::class, 'guardarConfiguracion'])->name('configuracion.guardar');
});
