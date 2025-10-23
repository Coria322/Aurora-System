<?php

use App\Http\Controllers\landingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/',[landingController::class, 'showHome'])->name('home');

Route::prefix('dashboard')->group(function () {
    
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified', 'redirect'])->name('dashboard');
    
    Route::get('/admin', function () {
        return Inertia::render('AdminBoard');
    })->middleware(['auth', 'role:admin'])->name('admin.dashboard');
    
    Route::get('/employee', function () {
        return Inertia::render('EmployeeBoard');
    })->middleware(['auth', 'role:empleado'])->name('employee.dashboard');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
