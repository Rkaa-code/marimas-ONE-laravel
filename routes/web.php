<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventaris\AsetController;
use App\Http\Controllers\Inventaris\JenisAsetController;
use App\Http\Controllers\Inventaris\KelengkapanMasterController;
use App\Http\Controllers\Inventaris\SupplierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        Route::resource('aset', AsetController::class);
    
        Route::prefix('master')->name('master.')->group(function () {
            Route::resource('jenis-aset', JenisAsetController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->parameters(['jenis-aset' => 'jenisAset']);
    
            Route::resource('supplier', SupplierController::class)
                ->only(['index', 'store', 'update', 'destroy']);
    
            Route::resource('kelengkapan', KelengkapanMasterController::class)
                ->only(['index', 'store', 'update', 'destroy']);
        });
    });
});
