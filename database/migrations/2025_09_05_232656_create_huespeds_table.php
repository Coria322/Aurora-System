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
        Schema::create('huespedes', function (Blueprint $table) {
            $table->id('id_huesped');
            $table->string('nombre', 50);
            $table->string('apellido_paterno', 50);
            $table->string('apellido_materno', 50);
            $table->string('documento_identidad', 20);
            $table->enum('tipo_documento', ['INE', 'Pasaporte', 'Cédula Profesional', 'Otro']);
            $table->string('telefono', 10)->nullable();
            $table->string('email', 100)->nullable();
            $table->date('fecha_nacimiento');
            $table->string('direccion', 150);
            $table->string('ciudad', 50);
            $table->string('pais', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huespedes');
    }
};
