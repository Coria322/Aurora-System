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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva')->primary();
            
            $table->unsignedBigInteger('id_huesped');
            $table->unsignedBigInteger('id_usuario');

            $table->foreign('id_huesped')
                    ->references('id_huesped')
                    ->on('huespedes')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            
            $table->foreign('id_usuario')
                    ->references('id_usuario')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->date('fecha_checkin');
            $table->date('fecha_checkout');
            $table->integer('cantidad_personas');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2);
            $table->decimal('total', 10, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
