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
        Schema::table('region_whatsapp_numbers', function (Blueprint $table) {
            // Tienda 4Life de ESTE agente (su propio código de referido) — si la
            // deja vacía, se usa la tienda general de la región como respaldo.
            $table->string('tienda_url')->nullable()->after('numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_whatsapp_numbers', function (Blueprint $table) {
            $table->dropColumn('tienda_url');
        });
    }
};
