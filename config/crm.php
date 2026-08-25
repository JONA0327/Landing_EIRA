<?php

return [

    // Base de la API pública del CRM, SIN el slug del tenant al final.
    // Ej: si el CRM está en https://midominio.com, esto es https://midominio.com/api/v1
    'base_url' => env('CRM_BASE_URL', ''),

    // Slug del tenant/negocio dentro del CRM.
    'tenant_slug' => env('CRM_TENANT_SLUG', ''),

    // API Key de solo lectura (Configuración → Prompt → API Key de consulta en el CRM).
    // A propósito NO se usa el API Token general aquí — este proyecto solo necesita
    // LEER el catálogo, nunca escribir, así que si esta key se filtra no compromete
    // nada más que la lectura de catálogos.
    'catalog_api_key' => env('CRM_CATALOG_API_KEY', ''),

    // Slug del módulo de catálogo a mostrar en la landing (el que aparece en
    // el sidebar "Catálogos" del CRM, ej. "productos_mxn").
    'catalog_module' => env('CRM_CATALOG_MODULE', ''),

];
