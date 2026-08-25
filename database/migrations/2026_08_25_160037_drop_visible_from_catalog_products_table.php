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
        // La visibilidad ahora vive en la tabla pivote catalog_product_region
        // (un producto "visible" es uno con al menos una región asociada).
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->dropColumn('visible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->boolean('visible')->default(false);
        });
    }
};
