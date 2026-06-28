<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SupplierController;
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

    // Clientes — lectura para autenticados; escritura Administrador o Cajero
    Route::get('customers', [CustomerController::class, 'index'])->name('api.customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('api.customers.show');
    Route::middleware('role:Administrador|Cajero')->group(function () {
        Route::post('customers', [CustomerController::class, 'store'])->name('api.customers.store');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('api.customers.update');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('api.customers.destroy');
    });

    // Proveedores — lectura para autenticados; escritura solo Administrador
    Route::get('suppliers', [SupplierController::class, 'index'])->name('api.suppliers.index');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('api.suppliers.show');
    Route::middleware('role:Administrador')->group(function () {
        Route::post('suppliers', [SupplierController::class, 'store'])->name('api.suppliers.store');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('api.suppliers.update');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('api.suppliers.destroy');
    });

    // Categorías — lectura para autenticados; escritura solo Administrador
    Route::get('categories', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('api.categories.show');
    Route::middleware('role:Administrador')->group(function () {
        Route::post('categories', [CategoryController::class, 'store'])->name('api.categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('api.categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('api.categories.destroy');
    });
});
