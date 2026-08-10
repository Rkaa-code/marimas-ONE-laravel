<?php

use App\Http\Controllers\Inventaris\AsetController;
use App\Http\Controllers\Inventaris\JenisAsetController;
use App\Http\Controllers\Inventaris\KelengkapanMasterController;
use App\Http\Controllers\Inventaris\SupplierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/inventaris/aset');

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
