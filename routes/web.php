<?php

use App\Http\Controllers\landingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/',[landingController::class, 'showHome'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'redirect'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
