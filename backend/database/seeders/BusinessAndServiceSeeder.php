<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Service;
use App\Models\BusinessHour; // <-- IMPORTANTE: Importar el nuevo modelo
use Illuminate\Support\Facades\Schema;

class BusinessAndServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Desactivar restricciones de llaves foráneas para limpiar todo en orden
        Schema::disableForeignKeyConstraints();
        BusinessHour::truncate(); // <-- Limpiar la nueva tabla de horarios
        Service::truncate();
        Business::truncate();
        Schema::enableForeignKeyConstraints();

        // ==========================================
        // 1. BARBERÍA OLYMPUS
        // ==========================================
        $barberia = Business::create([
            'name' => 'Barbería Olympus',
            'slug' => 'barberia-olympus',
            'email' => 'contacto@olympus.test',
        ]);

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

        // Horarios Barbería: Lunes (1) a Sábado (6) de 9:00 AM a 7:00 PM
        for ($day = 1; $day <= 6; $day++) {
            BusinessHour::create([
                'business_id' => $barberia->id,
                'day_of_week' => $day,
                'opening_time' => '09:00:00',
                'closing_time' => '19:00:00',
                'is_closed' => false,
            ]);
        }
        // Domingo (0) Cerrado
        BusinessHour::create([
            'business_id' => $barberia->id,
            'day_of_week' => 0,
            'is_closed' => true,
        ]);


        // ==========================================
        // 2. CLÍNICA DENTAL SONRISAS
        // ==========================================
        $clinica = Business::create([
            'name' => 'Clínica Dental Sonrisas',
            'slug' => 'clinica-dental-sonrisas',
            'email' => 'info@sonrisasdental.test',
        ]);

        Service::create([
            'business_id' => $clinica->id,
            'name' => 'Limpieza Dental Ultrasónica',
            'description' => 'Eliminación de sarro y pulido dental.',
            'duration_minutes' => 45,
            'price' => 25.00,
        ]);

        // Horarios Clínica: Lunes (1) a Viernes (5) de 8:00 AM a 5:00 PM. Sábados (6) de 8:00 AM a 12:00 PM.
        for ($day = 1; $day <= 5; $day++) {
            BusinessHour::create([
                'business_id' => $clinica->id,
                'day_of_week' => $day,
                'opening_time' => '08:00:00',
                'closing_time' => '17:00:00',
                'is_closed' => false,
            ]);
        }
        BusinessHour::create([
            'business_id' => $clinica->id,
            'day_of_week' => 6,
            'opening_time' => '08:00:00',
            'closing_time' => '12:00:00',
            'is_closed' => false,
        ]);
        BusinessHour::create([
            'business_id' => $clinica->id,
            'day_of_week' => 0,
            'is_closed' => true,
        ]);


        // ==========================================
        // 3. TALLER AUTOFIX
        // ==========================================
        $taller = Business::create([
            'name' => 'Taller AutoFix',
            'slug' => 'taller-autofix',
            'email' => 'soporte@autofix.test',
        ]);

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

        // Horarios Taller: Lunes (1) a Sábado (6) corrido de 8:00 AM a 4:00 PM
        for ($day = 1; $day <= 6; $day++) {
            BusinessHour::create([
                'business_id' => $taller->id,
                'day_of_week' => $day,
                'opening_time' => '08:00:00',
                'closing_time' => '16:00:00',
                'is_closed' => false,
            ]);
        }
        BusinessHour::create([
            'business_id' => $taller->id,
            'day_of_week' => 0,
            'is_closed' => true,
        ]);
    }
}
