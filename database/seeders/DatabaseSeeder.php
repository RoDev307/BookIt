<?php

namespace Database\Seeders;

use App\Models\User; // <-- Asegúrate de que tenga el modelo User importado
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Desactivar llaves foráneas y limpiar la tabla de usuarios para evitar duplicados
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Crear el usuario de prueba de Laravel (ahora sí se limpiará antes de entrar)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // 3. Llamar a tu seeder de negocios y servicios que ya incluye a AutoFix
        $this->call([
            BusinessAndServiceSeeder::class,
        ]);
    }
}
