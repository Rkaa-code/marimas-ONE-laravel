<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_id')->constrained('jenis_aset');
            $table->foreignId('supplier_id')->nullable()->constrained('supplier');

            $table->string('merek')->nullable();
            $table->string('tipe')->nullable();
            $table->string('warna')->nullable();
            $table->string('serial_number')->nullable();

            $table->date('tanggal_garansi')->nullable();
            $table->date('tanggal_pembelian')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->string('no_good_receive')->nullable();

            $table->string('perusahaan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();

            $table->enum('status', [
                'tersedia',
                'dipakai',
                'menunggu_perbaikan',
                'sedang_diperbaiki',
                'rusak_berat',
                'dijual',
            ])->default('tersedia');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
