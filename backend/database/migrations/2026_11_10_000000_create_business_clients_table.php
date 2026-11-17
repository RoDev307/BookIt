<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone', 40)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->unique(['business_id', 'user_id']);
            $table->index(['business_id', 'name']);
            $table->index(['business_id', 'phone']);
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('appointments', function (Blueprint $table) { $table->dropConstrainedForeignId('client_id'); });
        Schema::dropIfExists('clients');
    }
};
