<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificamos si ya existe para no duplicarlo en Aiven
        $exists = User::where('email', 'admin@bookit.com')->exists();

        if (!$exists) {
            User::create([
                'name' => 'Administrador Maestro',
                'email' => 'admin@bookit.com', // El correo que usarás para loguearte
                'password' => Hash::make('itca123#'), // Laravel la encriptará automáticamente
                'role' => 'super_admin', // 👈 El rol exacto que lee tu middleware CheckRole
                'business_id' => null, // Al ser el admin maestro global, no pertenece a ningún comercio local
            ]);
        }
    }
}
