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
        Schema::table('businesses', function (Blueprint $blueprint) {
            // Agregamos las dos columnas faltantes como opcionales (nullable)
            $blueprint->text('description')->nullable()->after('email');
            $blueprint->string('image_url')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['description', 'image_url']);
        });
    }
};
