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
        Schema::create('tipo_habitaciones', function (Blueprint $table) {
            
            $table->id('id_tipo_habitacion');
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->integer('capacidad_maxima');
            $table->decimal('precio_noche', 8, 2);
            $table->text('servicios_incluidos')->nullable();
            $table->boolean('activo')->default(true);
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_habitacions');
    }
};
