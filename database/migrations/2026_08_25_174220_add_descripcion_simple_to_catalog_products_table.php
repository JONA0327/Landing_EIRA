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
        Schema::table('catalog_products', function (Blueprint $table) {
            // Versión corta/en puntos generada con IA (o editada a mano por el
            // admin) a partir de la descripción original — nunca se sobreescribe
            // el campo original del catálogo (datos->descripcion).
            $table->text('descripcion_simple')->nullable()->after('datos');
            $table->timestamp('descripcion_simple_generada_en')->nullable()->after('descripcion_simple');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->dropColumn(['descripcion_simple', 'descripcion_simple_generada_en']);
        });
    }
};
