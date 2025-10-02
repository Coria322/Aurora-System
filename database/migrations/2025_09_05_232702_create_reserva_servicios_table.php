<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas_servicios', function (Blueprint $table) {
            $table->id('id_reserva_servicio');
            

            $table->unsignedBigInteger('id_reserva');
            $table->unsignedBigInteger('id_servicio');

            $table->foreign('id_reserva')
                    ->references('id_reserva')
                    ->on('reservas')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            
            $table->foreign('id_servicio')
                    ->references('id_servicio')
                    ->on('servicios')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->integer('cantidad');

            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->date('fecha_servicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas_servicios');
    }
};
