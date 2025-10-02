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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');

            $table->unsignedBigInteger('id_reserva');
            
            $table->foreign('id_reserva')
                  ->references('id_reserva')->on('reservas')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->decimal('monto', 10, 2);

            $table->enum('metodo_pago', ['tarjeta_credito', 'paypal', 'transferencia_bancaria', 'efectivo']);    
            $table->enum('estado_pago', ['pendiente', 'completado', 'fallido'])->default('pendiente');

            $table->string('referencia_transaccion')->unique();
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
