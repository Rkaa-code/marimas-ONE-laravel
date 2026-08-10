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
use App\Http\Controllers\NotificationController;
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

        Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('terbaru', [NotificationController::class, 'terbaru'])->name('terbaru');
            Route::post('baca-semua', [NotificationController::class, 'bacaSemua'])->name('baca-semua');
            Route::post('{notification}/baca', [NotificationController::class, 'baca'])->name('baca');
        });
    });

    Route::prefix('inventaris')->name('inventaris.')->group(function () {
        // Kelola aset (tambah/edit/hapus) & serahkan/pinjamkan ke role lain -- admin aja.
        // HARUS didaftarkan sebelum resource index/show, karena route create/edit
        // punya segmen statis ("create", "{aset}/edit") yang harus menang duluan
        // sebelum ketangkep sama wildcard {aset} di route show.
        Route::middleware('admin')->group(function () {
            Route::delete('aset/bulk-destroy', [AsetController::class, 'bulkDestroy'])->name('aset.bulk-destroy');
            Route::resource('aset', AsetController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

            Route::prefix('aset/{aset}')->name('aset.')->group(function () {
                Route::get('cari-penerima', [AsetPemakaiController::class, 'cariPenerima'])->name('cari-penerima');
                Route::post('serahkan', [AsetPemakaiController::class, 'store'])->name('serahkan');
            });

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

        // Lihat daftar & detail aset -- boleh semua role yang login.
        // Route show ({aset}) ditaruh belakangan biar nggak nabrak route create di atas.
        Route::resource('aset', AsetController::class)->only(['index', 'show']);

        // Kembalikan & lapor rusak -- self-service, boleh dipencet user yang lagi pegang aset-nya.
        Route::post('aset/{aset}/lapor-rusak', [AsetPenangananController::class, 'store'])->name('aset.lapor-rusak');
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
    });
});