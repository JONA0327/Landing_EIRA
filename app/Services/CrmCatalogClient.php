<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente de solo lectura para la API pública de catálogos del CRM
 * (CRM_AUTOMATIZADOR). Usa la CRM_CATALOG_API_KEY (dedicada, solo lectura)
 * — nunca el API Token general del CRM.
 *
 * Cada valor primero revisa Setting (lo que el admin haya guardado desde el
 * panel Configuración) y si no hay nada ahí, cae al .env — así una edición
 * desde el panel aplica al instante sin necesitar redeploy.
 */
class CrmCatalogClient
{
    private function baseUrlConfigurada(): string
    {
        return Setting::get('crm_base_url', config('crm.base_url')) ?? '';
    }

    private function tenantSlug(): ?string
    {
        return Setting::get('crm_tenant_slug', config('crm.tenant_slug'));
    }

    private function apiKey(): ?string
    {
        return Setting::get('crm_catalog_api_key', config('crm.catalog_api_key'));
    }

    private function baseUrl(): string
    {
        return rtrim($this->baseUrlConfigurada(), '/') . '/' . $this->tenantSlug();
    }

    private function configured(): bool
    {
        return (bool) $this->baseUrlConfigurada() && (bool) $this->tenantSlug() && (bool) $this->apiKey();
    }

    private function get(string $path, array $params = []): array
    {
        if (! $this->configured()) {
            return ['success' => false, 'error' => 'Conexión al CRM no configurada — revísala en el panel, pestaña Configuración.'];
        }

        try {
            $response = Http::withHeaders(['X-API-Key' => $this->apiKey()])
                ->timeout(15)
                ->get($this->baseUrl() . '/' . ltrim($path, '/'), $params);

            $data = $response->json();

            if (! is_array($data)) {
                return ['success' => false, 'error' => 'Respuesta no-JSON del CRM (HTTP ' . $response->status() . ').'];
            }

            if (! $response->successful()) {
                Log::warning('[CrmCatalogClient] HTTP ' . $response->status() . ' en ' . $path, $data);
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('[CrmCatalogClient] Error consultando ' . $path . ': ' . $e->getMessage());
            return ['success' => false, 'error' => 'No se pudo conectar con el CRM: ' . $e->getMessage()];
        }
    }

    /**
     * Lista los catálogos activos del tenant y sus campos.
     * GET /{tenant}/modulos
     *
     * Devuelve ['success' => bool, 'data' => array, 'error' => ?string].
     */
    public function modulos(): array
    {
        $data = $this->get('modulos');
        return [
            'success' => $data['success'] ?? false,
            'data'    => $data['data'] ?? [],
            'error'   => $data['error'] ?? null,
        ];
    }

    /**
     * Lista los registros de un catálogo específico.
     * GET /{tenant}/{modulo}
     *
     * Devuelve ['success' => bool, 'data' => array, 'error' => ?string].
     */
    public function catalogo(string $moduloSlug, array $params = []): array
    {
        $data = $this->get($moduloSlug, array_merge(['per_page' => 200], $params));
        return [
            'success' => $data['success'] ?? false,
            'data'    => $data['data'] ?? [],
            'error'   => $data['error'] ?? null,
        ];
    }
}
