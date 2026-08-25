<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Crea (o actualiza la contraseña de) el único usuario admin del panel oculto,
 * a partir de ADMIN_EMAIL / ADMIN_PASSWORD del .env. Vuelve a correrlo
 * (php artisan db:seed --class=AdminUserSeeder) para cambiar la contraseña.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email    = config('panel.admin_email');
        $password = config('panel.admin_password');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD no están definidos en el .env — no se creó ningún usuario.');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Administrador', 'password' => bcrypt($password)]
        );

        $this->command?->info("Usuario admin listo: {$email}");
    }
}
