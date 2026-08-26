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
            $table->string('direccion', 500)->nullable()->after('codigo_4life');
            $table->string('direccion_corta', 100)->nullable()->after('direccion');
            $table->double('lat')->nullable()->after('direccion_corta');
            $table->double('lng')->nullable()->after('lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_whatsapp_numbers', function (Blueprint $table) {
            $table->dropColumn(['direccion', 'direccion_corta', 'lat', 'lng']);
        });
    }
};
