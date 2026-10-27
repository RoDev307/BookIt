<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function column_exists(): void
    {
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            // Relación con la tabla de negocios (SaaS Multi-tenant)
            $table->foreignId('business_id')->constrained()->onDelete('cascade');

            // Día de la semana: 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
            $table->unsignedTinyInteger('day_of_week');

            // Horarios de atención
            $table->time('opening_time')->nullable(); // Ejemplo: 08:00:00
            $table->time('closing_time')->nullable(); // Ejemplo: 18:00:00

            // Bandera para saber si el negocio cierra ese día (ej. Domingos)
            $table->boolean('is_closed')->default(false);

            $table->timestamps();

            // Clave única compuesta: Un negocio no puede tener el mismo día duplicado
            $table->unique(['business_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};
