<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente de solo lectura para la API pública de catálogos del CRM
 * (CRM_AUTOMATIZADOR). Usa la CRM_CATALOG_API_KEY (dedicada, solo lectura)
 * configurada en config/crm.php — nunca el API Token general del CRM.
 */
class CrmCatalogClient
{
    private function baseUrl(): string
    {
        return rtrim((string) config('crm.base_url'), '/') . '/' . config('crm.tenant_slug');
    }

    private function configured(): bool
    {
        return (bool) config('crm.base_url') && (bool) config('crm.tenant_slug') && (bool) config('crm.catalog_api_key');
    }

    private function get(string $path, array $params = []): array
    {
        if (! $this->configured()) {
            return ['success' => false, 'error' => 'Conexión al CRM no configurada (revisa CRM_BASE_URL, CRM_TENANT_SLUG, CRM_CATALOG_API_KEY en el .env).'];
        }

        try {
            $response = Http::withHeaders(['X-API-Key' => config('crm.catalog_api_key')])
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
