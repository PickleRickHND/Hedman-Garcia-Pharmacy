<?php

use App\Http\Controllers\Billing\InvoicePdfController;
use App\Livewire\Auth\ChangePasswordRequired;
use App\Livewire\Billing\InvoiceList;
use App\Livewire\Billing\NewInvoice;
use App\Livewire\Billing\Show as InvoiceShow;
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Customers\Create as CustomerCreate;
use App\Livewire\Customers\Edit as CustomerEdit;
use App\Livewire\Customers\Index as CustomerIndex;
use App\Livewire\Customers\Show as CustomerShow;
use App\Livewire\Suppliers\Create as SupplierCreate;
use App\Livewire\Suppliers\Edit as SupplierEdit;
use App\Livewire\Suppliers\Index as SupplierIndex;
use App\Livewire\CashRegister\Close as CashRegisterClose;
use App\Livewire\CashRegister\Index as CashRegisterIndex;
use App\Livewire\CashRegister\Show as CashRegisterShow;
use App\Livewire\Dashboard;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\Reports\InventoryReport;
use App\Livewire\Reports\ProductsReport;
use App\Livewire\Reports\SalesReport;
use App\Livewire\Returns\Create as ReturnCreate;
use App\Livewire\Returns\Index as ReturnIndex;
use App\Livewire\Returns\Show as ReturnShow;
use App\Livewire\Inventory\StockMovements;
use App\Livewire\Products\Create as ProductCreate;
use App\Livewire\Products\Edit as ProductEdit;
use App\Livewire\Products\Index as ProductIndex;
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

// Reportes — solo Administrador
Route::middleware(['auth', 'role:Administrador'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', ReportsIndex::class)->name('index');
    Route::get('sales', SalesReport::class)->name('sales');
    Route::get('products', ProductsReport::class)->name('products');
    Route::get('inventory', InventoryReport::class)->name('inventory');
});

// Categorías y Proveedores — solo Administrador
Route::middleware(['auth', 'role:Administrador'])->group(function () {
    Route::get('categories', CategoryIndex::class)->name('categories.index');

    Route::get('suppliers', SupplierIndex::class)->name('suppliers.index');
    Route::get('suppliers/create', SupplierCreate::class)->name('suppliers.create');
    Route::get('suppliers/{supplier}/edit', SupplierEdit::class)->name('suppliers.edit');
});

// Clientes — Administrador y Cajero
Route::middleware(['auth', 'role:Administrador|Cajero'])->group(function () {
    Route::get('customers', CustomerIndex::class)->name('customers.index');
    Route::get('customers/create', CustomerCreate::class)->name('customers.create');
    Route::get('customers/{customer}', CustomerShow::class)->name('customers.show');
    Route::get('customers/{customer}/edit', CustomerEdit::class)->name('customers.edit');
});

// Inventario — Administrador y Cajero pueden ver; solo Admin puede crear/editar
Route::middleware(['auth', 'role:Administrador|Cajero'])->group(function () {
    Route::get('inventory', ProductIndex::class)->name('inventory.index');
    Route::get('inventory/products', ProductIndex::class)->name('products.index');
    Route::get('inventory/movements', StockMovements::class)->name('inventory.movements');

    Route::middleware('role:Administrador')->group(function () {
        Route::get('inventory/products/create', ProductCreate::class)->name('products.create');
        Route::get('inventory/products/{product}/edit', ProductEdit::class)->name('products.edit');
    });

    // Corte de caja
    Route::get('cash-register', CashRegisterIndex::class)->name('cash-register.index');
    Route::get('cash-register/{register}/close', CashRegisterClose::class)->name('cash-register.close');
    Route::get('cash-register/{register}', CashRegisterShow::class)->name('cash-register.show');

    // Devoluciones
    Route::get('returns', ReturnIndex::class)->name('returns.index');
    Route::get('returns/create/{invoice}', ReturnCreate::class)->name('returns.create');
    Route::get('returns/{returnOrder}', ReturnShow::class)->name('returns.show');

    // Facturación — ambos roles
    Route::get('billing', InvoiceList::class)->name('billing.index');
    Route::get('billing/new', NewInvoice::class)->name('billing.create');
    Route::get('billing/{invoice}', InvoiceShow::class)->name('billing.show');
    Route::get('billing/{invoice}/pdf', InvoicePdfController::class)->name('billing.pdf');
});

require __DIR__.'/auth.php';
