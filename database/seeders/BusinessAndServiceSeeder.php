<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;

class BusinessAndServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Desactivar restricciones de llaves foráneas para poder limpiar las tablas sin errores
        Schema::disableForeignKeyConstraints();
        Service::truncate();
        Business::truncate();
        Schema::enableForeignKeyConstraints();

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

        // 3. Crear el tercer negocio requerido: Taller AutoFix
        $taller = Business::create([
            'name' => 'Taller AutoFix',
            'slug' => 'taller-autofix',
            'email' => 'soporte@autofix.test',
        ]);

        // Servicios para el taller mecánico
        Service::create([
            'business_id' => $taller->id,
            'name' => 'Cambio de Aceite y Filtro',
            'description' => 'Incluye aceite sintético premium y revisión de niveles generales.',
            'duration_minutes' => 40,
            'price' => 45.00,
        ]);

        Service::create([
            'business_id' => $taller->id,
            'name' => 'Diagnóstico Computarizado',
            'description' => 'Escaneo completo de códigos de error del motor con escáner OBD2.',
            'duration_minutes' => 30,
            'price' => 20.00,
        ]);
    }
}
