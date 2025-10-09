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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id_usuario')->on('users')->onDelete('cascade');
            $table->string('room_type'); // suite-deluxe, habitacion-estandar, suite-familiar
            $table->string('room_name'); // Nombre de la habitación
            $table->decimal('price_per_night', 8, 2); // Precio por noche
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('total_nights'); // Número de noches
            $table->decimal('total_amount', 10, 2); // Monto total
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('special_requests')->nullable(); // Solicitudes especiales
            $table->string('guest_name'); // Nombre del huésped
            $table->string('guest_email'); // Email del huésped
            $table->string('guest_phone')->nullable(); // Teléfono del huésped
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
