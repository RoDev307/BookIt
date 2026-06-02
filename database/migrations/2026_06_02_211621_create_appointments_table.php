<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            // Relaciones obligatorias
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // El cliente
            $table->foreignId('business_id')->constrained()->onDelete('cascade'); // El negocio
            $table->foreignId('service_id')->constrained()->onDelete('cascade'); // El servicio elegido

            // Datos de la cita
            $table->dateTime('appointment_time'); // Fecha y hora de la cita
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable(); // Notas que deje el cliente
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
