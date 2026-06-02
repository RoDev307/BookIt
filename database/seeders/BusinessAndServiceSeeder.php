<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Service;

class BusinessAndServiceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear un negocio de Barbería
        $barberia = Business::create([
            'name' => 'Barbería Olympus',
            'slug' => 'barberia-olympus',
            'email' => 'contacto@olympus.test',
        ]);

        // Servicios para la barbería
        Service::create([
            'business_id' => $barberia->id,
            'name' => 'Corte de Cabello Clásico',
            'description' => 'Corte con tijera o máquina más lavado premium.',
            'duration_minutes' => 30,
            'price' => 10.00,
        ]);

        Service::create([
            'business_id' => $barberia->id,
            'name' => 'Perfilado de Barba',
            'description' => 'Diseño y afeitado con toalla caliente.',
            'duration_minutes' => 20,
            'price' => 6.00,
        ]);

        // 2. Crear un negocio de Clínica
        $clinica = Business::create([
            'name' => 'Clínica Dental Sonrisas',
            'slug' => 'clinica-dental-sonrisas',
            'email' => 'info@sonrisasdental.test',
        ]);

        // Servicios para la clínica
        Service::create([
            'business_id' => $clinica->id,
            'name' => 'Limpieza Dental Ultrasónica',
            'description' => 'Eliminación de sarro y pulido dental.',
            'duration_minutes' => 45,
            'price' => 25.00,
        ]);
    }
}
