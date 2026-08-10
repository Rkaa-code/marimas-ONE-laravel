<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AsetPemakaiController;
use App\Http\Controllers\AsetPenangananController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FotoAsetController;
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

    Route::middleware('admin')->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });

    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        Route::resource('aset', AsetController::class);

        Route::prefix('aset/{aset}')->name('aset.')->group(function () {
            Route::get('cari-penerima', [AsetPemakaiController::class, 'cariPenerima'])->name('cari-penerima');
            Route::post('serahkan', [AsetPemakaiController::class, 'store'])->name('serahkan');
            Route::post('lapor-rusak', [AsetPenangananController::class, 'store'])->name('lapor-rusak');
        });
        Route::post('aset-pemakai/{pemakai}/kembalikan', [AsetPemakaiController::class, 'kembalikan'])->name('aset.pemakai.kembalikan');
        Route::get('aset-pemakai/{pemakai}/struk', [AsetPemakaiController::class, 'struk'])->name('aset.pemakai.struk');
        Route::get('aset-pemakai/{pemakai}/struk-kembali', [AsetPemakaiController::class, 'strukKembali'])->name('aset.pemakai.struk-kembali');

        Route::prefix('penanganan-aset')->name('penanganan-aset.')->group(function () {
            Route::get('/', [AsetPenangananController::class, 'index'])->name('index');
            Route::get('{penanganan}', [AsetPenangananController::class, 'show'])->name('show');
            Route::post('{penanganan}/terima', [AsetPenangananController::class, 'terima'])->name('terima');
            Route::post('{penanganan}/selesai', [AsetPenangananController::class, 'selesai'])->name('selesai');
        });

        Route::get('foto-aset', [FotoAsetController::class, 'index'])->name('foto-aset.index');

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