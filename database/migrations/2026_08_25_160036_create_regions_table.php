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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 5)->unique(); // mx, co, us, cl
            $table->string('nombre');
            $table->string('bandera', 10); // emoji de bandera
            $table->string('whatsapp_numero')->nullable(); // solo dígitos, formato E.164 sin "+"
            $table->string('codigo_4life')->nullable(); // código de referido/distribuidor 4Life
            $table->string('tienda_url')->nullable(); // URL de la tienda 4Life de ese país
            $table->text('direccion')->nullable(); // dirección completa (sección Ubicación)
            $table->string('direccion_corta')->nullable(); // ej. "Apodaca, NL" (footer)
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
