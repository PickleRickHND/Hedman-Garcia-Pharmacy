<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Rutas de administracion - solo rol Administrador
Route::middleware(['auth', 'role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('users', 'admin.placeholder')->name('users.index');
    // Aqui iran los modulos de gestion de usuarios en Fase 2
});

// Rutas de cajero - Administrador o Cajero
Route::middleware(['auth', 'role:Administrador|Cajero'])->group(function () {
    Route::view('billing', 'billing.placeholder')->name('billing.index');
    Route::view('inventory', 'inventory.placeholder')->name('inventory.index');
    // Modulos de Fase 3 y 4
});

require __DIR__.'/auth.php';
