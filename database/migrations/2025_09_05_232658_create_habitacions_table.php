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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id('id_habitacion')->primary();
            $table->string('numero_habitacion')->unique();
            
            $table->unsignedBigInteger('id_tipo_habitacion');
            
            $table->foreign('id_tipo_habitacion')
                    ->references('id_tipo_habitacion')
                    ->on('tipo_habitaciones')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->enum('estado', ['disponible','ocupada','no disponible']);
            $table->integer('piso');
            $table->text('descripcion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
