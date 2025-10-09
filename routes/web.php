<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ReservationController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas para el sistema de reservas
Route::middleware(['auth', 'verified'])->group(function () {
    // API de reservas
    Route::get('/api/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/api/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/api/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::put('/api/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::patch('/api/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    
    // Tipos de habitación
    Route::get('/api/room-types', [ReservationController::class, 'getRoomTypes'])->name('room-types.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
