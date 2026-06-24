<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArchivedController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    Route::get('/set-password/{token}', [SetPasswordController::class, 'showSetPasswordForm'])->name('auth.set-password');
    Route::post('/set-password/{token}', [SetPasswordController::class, 'setPassword']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', fn() => redirect()->route('kanban.index'));
    Route::get('/dashboard', fn() => redirect()->route('kanban.index'))->name('dashboard');

    // Kanban
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/kanban/orders', [KanbanController::class, 'store'])->name('kanban.orders.store');
    Route::get('/kanban/orders/{order}', [KanbanController::class, 'show'])->name('kanban.orders.show');
    Route::put('/kanban/orders/{order}/status', [KanbanController::class, 'updateStatus'])->name('kanban.orders.status');
    Route::post('/kanban/orders/{order}/notes', [KanbanController::class, 'addNote'])->name('kanban.orders.notes');
    Route::put('/kanban/orders/{order}', [KanbanController::class, 'update'])->name('kanban.orders.update');
    Route::post('/kanban/orders/{order}/dispatch', [KanbanController::class, 'dispatch'])->name('kanban.orders.dispatch');
    Route::post('/kanban/orders/{order}/archive', [KanbanController::class, 'archive'])->name('kanban.orders.archive');
    Route::post('/kanban/orders/{order}/restore', [KanbanController::class, 'restore'])->name('kanban.orders.restore');

    Route::get('/archived', [ArchivedController::class, 'index'])->name('archived.index');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    // Customer API (for order form)
    Route::get('/api/customers', [KanbanController::class, 'customers'])->name('api.customers');
    Route::get('/api/customers/{customer}/phones', [KanbanController::class, 'customerPhones'])->name('api.customers.phones');
    Route::get('/api/customers/{customer}/addresses', [KanbanController::class, 'customerAddresses'])->name('api.customers.addresses');
    Route::get('/api/drivers', [KanbanController::class, 'drivers'])->name('api.drivers');

    // Customers (admin + sales — enforced via permissions middleware when available)
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Admin-only routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Products, Fillings, Grinds (admin-manage, products_view for read)
    Route::middleware('admin')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');

        Route::post('/products', [ProductController::class, 'storeProduct'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroyProduct'])->name('products.destroy');

        Route::post('/fillings', [ProductController::class, 'storeFilling'])->name('fillings.store');
        Route::put('/fillings/{filling}', [ProductController::class, 'updateFilling'])->name('fillings.update');
        Route::delete('/fillings/{filling}', [ProductController::class, 'destroyFilling'])->name('fillings.destroy');

        Route::post('/grinds', [ProductController::class, 'storeGrind'])->name('grinds.store');
        Route::put('/grinds/{grind}', [ProductController::class, 'updateGrind'])->name('grinds.update');
        Route::delete('/grinds/{grind}', [ProductController::class, 'destroyGrind'])->name('grinds.destroy');
    });
});
