<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente para la API de Groq (compatible con el formato de OpenAI) — se usa
 * SOLO para generar una versión corta/en puntos clave de la descripción de
 * un producto del catálogo. Nunca toca el texto original: el resultado se
 * guarda aparte (catalog_products.descripcion_simple) y el admin puede
 * editarlo a mano después sin que se vuelva a regenerar.
 */
class GroqClient
{
    private const ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    private const SYSTEM_PROMPT = <<<'TXT'
Eres un redactor que simplifica descripciones de productos de bienestar (4Life) para una tarjeta de landing page, sin inventar datos que no estén en el texto original.

Responde ÚNICAMENTE con el resultado final. Nada de explicaciones, nada de markdown (sin **, sin #, sin backticks), sin comillas envolviendo el texto, sin repetir el nombre del producto como título.

Formato exacto:
- Línea 1: una frase breve (máx. 20 palabras) que resuma para qué sirve el producto.
- Después, entre 3 y 5 líneas, cada una empezando con "• " (viñeta + espacio), con los puntos clave más importantes del texto original.

No agregues nada antes ni después de eso.
TXT;

    /** Lo que el admin guardó en el panel (Configuración) manda; si no hay nada, se usa el .env. */
    private function apiKey(): ?string
    {
        return Setting::get('groq_api_key', config('services.groq.key'));
    }

    private function modelo(): string
    {
        return Setting::get('groq_model', config('services.groq.model', 'openai/gpt-oss-20b'));
    }

    public function configurado(): bool
    {
        return (bool) $this->apiKey();
    }

    /**
     * Devuelve la descripción simplificada, o null si Groq no está
     * configurado o la llamada falla (nunca lanza excepción).
     */
    public function simplificarDescripcion(string $nombreProducto, string $descripcionOriginal): ?string
    {
        if (! $this->configurado()) {
            return null;
        }

        $descripcionOriginal = trim($descripcionOriginal);
        if ($descripcionOriginal === '') {
            return null;
        }

        $modelo = $this->modelo();

        $payload = [
            'model' => $modelo,
            'temperature' => 0.4,
            // Los modelos "gpt-oss" razonan antes de responder y esos tokens
            // de razonamiento cuentan contra max_tokens — con un límite bajo
            // (300) se gastaban todo el presupuesto pensando y el contenido
            // final llegaba vacío o cortado (finish_reason=length). 600 deja
            // margen de sobra para una descripción corta + puntos.
            'max_tokens' => 600,
            'messages' => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user', 'content' => "Producto: {$nombreProducto}\n\nDescripción original:\n{$descripcionOriginal}"],
            ],
        ];

        // reasoning_effort es específico de la familia "gpt-oss" de Groq — con
        // "low" casi no gasta tokens en razonar, dejándolos todos para el
        // contenido. Solo se manda si el modelo configurado es de esa familia,
        // para no romper la llamada si el admin cambia a otro modelo.
        if (str_contains($modelo, 'gpt-oss')) {
            $payload['reasoning_effort'] = 'low';
        }

        try {
            $respuesta = Http::withToken($this->apiKey())
                ->timeout(20)
                ->post(self::ENDPOINT, $payload);

            if (! $respuesta->successful()) {
                Log::warning('[GroqClient] HTTP ' . $respuesta->status() . ': ' . $respuesta->body());
                return null;
            }

            $texto = $respuesta->json('choices.0.message.content');
            if (! is_string($texto) || trim($texto) === '') {
                Log::warning('[GroqClient] Respuesta sin contenido utilizable (finish_reason=' . $respuesta->json('choices.0.finish_reason') . '): ' . $respuesta->body());
                return null;
            }

            return $this->limpiar($texto);
        } catch (\Throwable $e) {
            Log::error('[GroqClient] Error generando descripción: ' . $e->getMessage());
            return null;
        }
    }

    /** Por si el modelo igual envuelve la respuesta en ```...``` o comillas, a pesar del prompt. */
    private function limpiar(string $texto): string
    {
        $texto = trim($texto);
        $texto = preg_replace('/^```[a-zA-Z]*\s*|\s*```$/', '', $texto);
        $texto = trim($texto, "\"'“”\n ");
        return trim($texto);
    }
}
