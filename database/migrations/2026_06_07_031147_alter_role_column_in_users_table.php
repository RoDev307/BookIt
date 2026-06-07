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
        Schema::table('users', function (Blueprint $table) {
            // SOLUCIÓN: Cambiamos la columna a string de longitud 50 para que 'super_admin' quepa holgadamente
            $table->string('role', 50)->default('client')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reversión opcional por si necesitas volver atrás
            $table->string('role', 20)->default('client')->change();
        });
    }
};
