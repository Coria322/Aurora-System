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
        Schema::create('check_in_out', function (Blueprint $table) {
            $table->id('id_check')->primary();

            $table->unsignedBigInteger('id_reserva');
            $table->unsignedBigInteger('id_usuario');

            $table->foreign('id_reserva')
                    ->references('id_reserva')
                    ->on('reservas')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->datetime('fecha_check_in');
            $table->datetime('fecha_check_out')->nullable();
            $table->text('observaciones_check_in')->nullable();
            $table->text('observaciones_check_out')->nullable();
            $table->enum('estado_check', ['check-in', 'check-out'])->default('check-in');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_in_out');
    }
};
