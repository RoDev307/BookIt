<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // SEGURIDAD: En lugar de usar truncate() que borra las cuentas de clientes reales (como Dina),
        // eliminamos únicamente las cuentas por defecto que el grupo controla para desarrollo.
        User::whereIn('email', [
            'test@example.com',
            'barberia@bookit.com',
            'clinica@bookit.com',
            'taller@bookit.com'
        ])->delete();

        // 1. Crear o actualizar el usuario de prueba de Laravel
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // 2. Llamar al seeder de comercios (Barbería, Clínica, AutoFix) de Omar
        $this->call([
            BusinessAndServiceSeeder::class,
        ]);

        // 3. Llamar al nuevo seeder multi-tenant de Alejandro para dar de alta los accesos de control
        $this->call([
            BusinessAdminsSeeder::class,
        ]);
    }
}
