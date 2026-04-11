<?php

use App\Http\Controllers\Billing\InvoicePdfController;
use App\Livewire\Auth\ChangePasswordRequired;
use App\Livewire\Billing\InvoiceList;
use App\Livewire\Billing\NewInvoice;
use App\Livewire\Billing\Show as InvoiceShow;
use App\Livewire\Dashboard;
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

// Inventario — Administrador y Cajero pueden ver; solo Admin puede crear/editar
Route::middleware(['auth', 'role:Administrador|Cajero'])->group(function () {
    Route::get('inventory', ProductIndex::class)->name('inventory.index');
    Route::get('inventory/products', ProductIndex::class)->name('products.index');

    Route::middleware('role:Administrador')->group(function () {
        Route::get('inventory/products/create', ProductCreate::class)->name('products.create');
        Route::get('inventory/products/{product}/edit', ProductEdit::class)->name('products.edit');
    });

    // Facturación — ambos roles
    Route::get('billing', InvoiceList::class)->name('billing.index');
    Route::get('billing/new', NewInvoice::class)->name('billing.create');
    Route::get('billing/{invoice}', InvoiceShow::class)->name('billing.show');
    Route::get('billing/{invoice}/pdf', InvoicePdfController::class)->name('billing.pdf');
});

require __DIR__.'/auth.php';
