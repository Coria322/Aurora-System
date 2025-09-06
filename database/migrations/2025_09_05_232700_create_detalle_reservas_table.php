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
        Schema::create('detalle_reservas', function (Blueprint $table) {
            
            $table->id('id_detalle_reserva')->primary();
            
            $table->unsignedBigInteger('id_reserva');
            
            $table->unsignedBigInteger('id_habitacion');
            
            $table->foreign('id_reserva')
                    ->references('id_reserva')
                    ->on('reservas')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->foreign('id_habitacion')
                    ->references('id_habitacion')
                    ->on('habitaciones')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('precio_noche', 8, 2);
            $table->integer('numero_noches');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_reservas');
    }
};
