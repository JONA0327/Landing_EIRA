<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('region_whatsapp_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('numero');
            $table->timestamps();
        });

        // Cada región tenía un solo número (regions.whatsapp_numero) — se
        // convierte en la primera fila de la nueva tabla antes de quitar la
        // columna vieja, para no perder lo que el admin ya había configurado.
        foreach (DB::table('regions')->whereNotNull('whatsapp_numero')->where('whatsapp_numero', '!=', '')->get() as $region) {
            DB::table('region_whatsapp_numbers')->insert([
                'region_id'  => $region->id,
                'numero'     => $region->whatsapp_numero,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('whatsapp_numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->string('whatsapp_numero')->nullable();
        });

        foreach (DB::table('region_whatsapp_numbers')->orderBy('id')->get() as $numero) {
            DB::table('regions')->where('id', $numero->region_id)->whereNull('whatsapp_numero')->update([
                'whatsapp_numero' => $numero->numero,
            ]);
        }

        Schema::dropIfExists('region_whatsapp_numbers');
    }
};
