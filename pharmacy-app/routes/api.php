<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CashRegisterController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Billing\InvoicePdfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — consumidas por el frontend Angular (token Bearer / Sanctum)
|--------------------------------------------------------------------------
*/

// Públicas
Route::post('login', [AuthController::class, 'login'])->name('api.login');

// Recuperación de contraseña por código (público + throttle anti-abuso)
Route::post('forgot-password', [PasswordResetController::class, 'forgot'])
    ->middleware('throttle:6,1')
    ->name('api.password.forgot');
Route::post('reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('throttle:6,1')
    ->name('api.password.reset');

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

    // ------------------------------------------------------------------
    // Facturación (POS) — todo bajo Administrador o Cajero; anular solo Admin
    // ------------------------------------------------------------------
    Route::middleware('role:Administrador|Cajero')->group(function () {
        Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('api.payment-methods.index');

        Route::get('invoices', [InvoiceController::class, 'index'])->name('api.invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('api.invoices.show');
        Route::post('invoices', [InvoiceController::class, 'store'])->name('api.invoices.store');
        Route::get('invoices/{invoice}/pdf', InvoicePdfController::class)->name('api.invoices.pdf');
    });

    // Anulación de factura — solo Administrador
    Route::middleware('role:Administrador')->group(function () {
        Route::post('invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('api.invoices.void');
    });

    // ------------------------------------------------------------------
    // Caja — apertura/cierre y consulta: Administrador o Cajero
    // ------------------------------------------------------------------
    Route::middleware('role:Administrador|Cajero')->group(function () {
        Route::get('cash-registers', [CashRegisterController::class, 'index'])->name('api.cash-registers.index');
        Route::get('cash-registers/current', [CashRegisterController::class, 'current'])->name('api.cash-registers.current');
        Route::get('cash-registers/{cashRegister}', [CashRegisterController::class, 'show'])->name('api.cash-registers.show');
        Route::post('cash-registers/open', [CashRegisterController::class, 'open'])->name('api.cash-registers.open');
        Route::post('cash-registers/{cashRegister}/close', [CashRegisterController::class, 'close'])->name('api.cash-registers.close');
    });

    // ------------------------------------------------------------------
    // Devoluciones — consulta: Admin o Cajero; crear: solo Administrador
    // ------------------------------------------------------------------
    Route::middleware('role:Administrador|Cajero')->group(function () {
        Route::get('returns', [ReturnController::class, 'index'])->name('api.returns.index');
        Route::get('returns/{return}', [ReturnController::class, 'show'])->name('api.returns.show');
    });
    Route::middleware('role:Administrador')->group(function () {
        Route::post('returns', [ReturnController::class, 'store'])->name('api.returns.store');
    });

    // ------------------------------------------------------------------
    // Kardex / movimientos de stock — solo lectura: Administrador o Cajero
    // ------------------------------------------------------------------
    Route::middleware('role:Administrador|Cajero')->group(function () {
        Route::get('stock-movements', [StockMovementController::class, 'index'])->name('api.stock-movements.index');
    });

    // ------------------------------------------------------------------
    // Dashboard — métricas + alertas (cualquier usuario autenticado)
    // ------------------------------------------------------------------
    Route::get('dashboard', DashboardController::class)->name('api.dashboard');

    // Notificaciones — alertas operativas en tiempo real (cualquier usuario autenticado)
    Route::get('notifications', NotificationsController::class)->name('api.notifications');

    // ------------------------------------------------------------------
    // Reportes — solo Administrador
    // ------------------------------------------------------------------
    Route::middleware('role:Administrador')->group(function () {
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('api.reports.sales');
        Route::get('reports/products', [ReportController::class, 'products'])->name('api.reports.products');
        Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('api.reports.inventory');
    });

    // ------------------------------------------------------------------
    // Usuarios — CRUD completo, solo Administrador
    // ------------------------------------------------------------------
    Route::middleware('role:Administrador')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('api.users.index');
        Route::get('roles', [UserController::class, 'roles'])->name('api.roles.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('api.users.show');
        Route::post('users', [UserController::class, 'store'])->name('api.users.store');
        Route::put('users/{user}', [UserController::class, 'update'])->name('api.users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('api.users.destroy');
    });
});
