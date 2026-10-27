<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BusinessAdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Administrador para la Barbería (business_id = 1)
        User::updateOrCreate(
            ['email' => 'barberia@bookit.com'],
            [
                'name'        => 'Admin Barbería Olympus',
                'password'    => Hash::make('barberia2026'),
                'role'        => 'admin_business',
                'business_id' => 1,
            ]
        );

        // 2. Administrador para la Clínica Dental (business_id = 2)
        User::updateOrCreate(
            ['email' => 'clinica@bookit.com'],
            [
                'name'        => 'Admin Clínica Sonrisas',
                'password'    => Hash::make('clinica2026'),
                'role'        => 'admin_business',
                'business_id' => 2,
            ]
        );

        // 3. Administrador para el Taller de Autos (business_id = 3)
        User::updateOrCreate(
            ['email' => 'taller@bookit.com'],
            [
                'name'        => 'Admin Taller AutoFix',
                'password'    => Hash::make('taller2026'),
                'role'        => 'admin_business',
                'business_id' => 3,
            ]
        );
    }
}
