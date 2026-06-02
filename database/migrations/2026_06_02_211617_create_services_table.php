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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            // Relación: Un servicio pertenece a un negocio específico
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Ej: "Corte de cabello", "Limpieza dental"
            $table->text('description')->nullable();
            $table->integer('duration_minutes'); // Ej: 30, 60, 90 (clave para el calendario)
            $table->decimal('price', 8, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
