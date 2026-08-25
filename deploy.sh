#!/usr/bin/env bash
#
# Despliegue de landing_4life en el VPS.
#
# Corre esto CADA VEZ que actualices el código en el servidor, en vez de ir
# ejecutando comandos sueltos a mano (así fue como se nos olvidó correr el
# seeder de regiones la vez pasada). Hace, en orden: trae el código nuevo,
# instala dependencias si cambiaron, migra la base de datos, siembra datos
# base (seguro de repetir, nunca pisa lo que ya configuraste en el panel),
# y sobre todo LIMPIA TODAS LAS CACHÉS DE LARAVEL — la causa más común de
# que "actualizo pero se sigue viendo lo de antes".
#
# Uso:  bash deploy.sh
#
set -e  # si algún paso falla, se detiene aquí en vez de seguir a ciegas

cd "$(dirname "$0")"

echo "== 1/7 Trayendo el código más reciente =="
git pull

echo "== 2/7 Instalando dependencias de PHP =="
composer install --no-dev --optimize-autoloader --no-interaction

echo "== 3/7 Corriendo migraciones =="
php artisan migrate --force

echo "== 4/7 Sembrando datos base (regiones, admin) — no pisa lo que ya configuraste =="
php artisan db:seed --class=RegionSeeder --force
php artisan db:seed --class=AdminUserSeeder --force

echo "== 5/7 Limpiando TODAS las cachés de Laravel (config, rutas, vistas, eventos) =="
php artisan optimize:clear

echo "== 6/7 Reconstruyendo cachés para producción (más rápido en cada visita) =="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "== 7/7 Reiniciando PHP-FPM para vaciar OPcache =="
# OPcache guarda el PHP ya compilado en memoria — si tu php.ini tiene
# opcache.validate_timestamps=0 (muy común en producción para más velocidad),
# PHP-FPM puede seguir sirviendo el código VIEJO en memoria aunque los
# archivos en disco ya cambiaron, hasta que se reinicia el servicio.
# Ajusta el nombre del servicio según tu PHP (php8.2-fpm, php8.3-fpm, etc.)
# — revísalo con: systemctl list-units --type=service | grep php
if command -v systemctl >/dev/null 2>&1; then
    PHP_FPM_SERVICE=$(systemctl list-units --type=service --no-legend 2>/dev/null | grep -o 'php[0-9.]*-fpm.service' | head -n1)
    if [ -n "$PHP_FPM_SERVICE" ]; then
        systemctl restart "$PHP_FPM_SERVICE"
        echo "   Reiniciado: $PHP_FPM_SERVICE"
    else
        echo "   No se encontró un servicio php-fpm activo — revisa el nombre a mano si tu página no refleja el cambio."
    fi
else
    echo "   systemctl no disponible — reinicia PHP-FPM manualmente si el cambio no se refleja."
fi

echo ""
echo "Listo. Si aun así ves contenido viejo:"
echo "  - Prueba en una ventana de incógnito (puede ser caché del navegador)."
echo "  - Si usas Cloudflare u otro CDN/proxy delante del sitio, purga su caché también."
