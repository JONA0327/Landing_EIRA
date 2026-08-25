<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Detecta el país de un visitante a partir de su IP (no de GPS/geolocalización
 * del navegador) — respeta VPN/proxy porque usa la IP real de la conexión, no
 * pide permiso al visitante, y no depende de APIs del navegador que algunos
 * modos de privacidad bloquean.
 *
 * Usa ip-api.com (gratis, sin API key, para uso no comercial de bajo volumen)
 * y cachea el resultado por IP 24h para no golpear el servicio en cada visita.
 */
class IpGeoService
{
    /** Código de país (2 letras, minúsculas — ej. "mx") o null si no se pudo determinar. */
    public function paisPorIp(string $ip): ?string
    {
        // IPs privadas/reservadas (127.0.0.1, LAN, dev local) no son geolocalizables.
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        return Cache::remember('ip-pais:' . $ip, now()->addDay(), function () use ($ip) {
            try {
                $respuesta = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,countryCode',
                ]);

                if ($respuesta->successful() && $respuesta->json('status') === 'success') {
                    $codigo = $respuesta->json('countryCode');
                    return $codigo ? strtolower($codigo) : null;
                }
            } catch (\Throwable $e) {
                Log::warning('[IpGeoService] No se pudo geolocalizar ' . $ip . ': ' . $e->getMessage());
            }

            return null;
        });
    }
}
