<?php

use App\Livewire\Auth\ChangePasswordRequired;
use App\Livewire\Dashboard;
use App\Livewire\Users\Create as UserCreate;
use App\Livewire\Users\Edit as UserEdit;
use App\Livewire\Users\Index as UserIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Cambio de password forzado — NO protegido por force.change.password middleware
Route::middleware(['auth'])->group(function () {
    Route::get('password/change-required', ChangePasswordRequired::class)
        ->name('password.change-required');
});

// Dashboard (Livewire)
Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Gestión de usuarios — solo rol Administrador
Route::middleware(['auth', 'role:Administrador'])->prefix('admin/users')->name('users.')->group(function () {
    Route::get('/', UserIndex::class)->name('index');
    Route::get('create', UserCreate::class)->name('create');
    Route::get('{user}/edit', UserEdit::class)->name('edit');
});

// Rutas compartidas — Administrador o Cajero
Route::middleware(['auth', 'role:Administrador|Cajero'])->group(function () {
    Route::view('billing', 'billing.placeholder')->name('billing.index');
    Route::view('inventory', 'inventory.placeholder')->name('inventory.index');
});

require __DIR__.'/auth.php';
