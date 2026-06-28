<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — consumidas por el frontend Angular (token Bearer / Sanctum)
|--------------------------------------------------------------------------
*/

// Públicas
Route::post('login', [AuthController::class, 'login'])->name('api.login');

// Protegidas por token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me'])->name('api.me');
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');

    // Productos — lectura para cualquier usuario autenticado
    Route::get('products', [ProductController::class, 'index'])->name('api.products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('api.products.show');

    // Productos — escritura solo Administrador (paridad con el rol exigido en Livewire)
    Route::middleware('role:Administrador')->group(function () {
        Route::post('products', [ProductController::class, 'store'])->name('api.products.store');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('api.products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('api.products.destroy');
    });
});
