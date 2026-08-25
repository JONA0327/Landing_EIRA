<?php

return [

    // Segmento de URL del panel admin oculto — NO se enlaza desde ningún
    // lado de la landing pública, solo quien conozca esta ruta puede llegar
    // a la pantalla de login. Cámbialo por algo no adivinable en producción.
    'path' => env('ADMIN_PANEL_PATH', 'panel-4life'),

    // Usados solo por database/seeders/AdminUserSeeder.php para crear/actualizar
    // el único usuario admin. No se leen en ningún otro lugar de la app.
    'admin_email'    => env('ADMIN_EMAIL'),
    'admin_password' => env('ADMIN_PASSWORD'),

];
