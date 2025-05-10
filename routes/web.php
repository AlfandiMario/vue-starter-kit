<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ElectricalController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Users Management Routes (Super Admin only)
Route::middleware(['auth', 'permission:create users'])->group(function () {
    Route::resource('users', UserController::class);
});

// Roles Management Routes (Super Admin only)
Route::middleware(['auth', 'permission:create roles'])->group(function () {
    Route::resource('roles', RoleController::class);
});

// Electricity System
Route::middleware(['auth', 'permission:monitor'])->group(function () {
    Route::get('electrical', [ElectricalController::class, 'index'])->name('electrical.index');
    Route::get('electrical/monitor', [ElectricalController::class, 'monitor'])->name('electrical.monitor');
    Route::get('electrical/control', [ElectricalController::class, 'control'])->name('electrical.control');
    Route::get('electrical/stats', [ElectricalController::class, 'stats'])->name('electrical.stats');
    Route::get('electrical/ike', [ElectricalController::class, 'ike'])->name('electrical.ike');
});

// Device Routes with different permission levels
Route::middleware(['auth', 'permission:view devices'])->group(function () {
    // These routes can be accessed by anyone with 'view devices' permission (all levels)
    Route::get('devices', [DeviceController::class, 'index'])->name('devices.index');

    // These routes can only be accessed by users with 'control devices' permission (admin and super-admin)
    Route::middleware('permission:control devices')->group(function () {
        Route::get('devices/create', [DeviceController::class, 'create'])->name('devices.create');
        Route::post('devices', [DeviceController::class, 'store'])->name('devices.store');
        Route::get('devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
        Route::put('devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
        Route::delete('devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
        Route::post('devices/{id}/control', [DeviceController::class, 'control'])->name('devices.control');
    });

    // This route must come after the specific routes to avoid conflicts
    Route::get('devices/{id}', [DeviceController::class, 'show'])->name('devices.show');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
