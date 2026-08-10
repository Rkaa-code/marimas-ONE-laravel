<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catatan serah-terima aset. Penerima langsung ke 'users' (difilter by
     * role di aplikasi: karyawan/hr/manajer untuk tab "Karyawan", cabang
     * untuk tab "Cabang") -- gak lewat tabel 'pekerja' biar satu jalur aja.
     *
     * Satu aset cuma boleh punya 1 baris aktif (tanggal_kembali null) dalam
     * satu waktu -- dijaga di controller pakai lock transaksi, bukan
     * constraint DB, karena partial unique index beda-beda caranya per
     * driver.
     */
    public function up(): void
    {
        Schema::create('aset_pemakai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')->constrained('aset')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('diserahkan_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('nomor_serah_terima')->unique();
            $table->date('tanggal_serah');
            $table->text('catatan_serah')->nullable();
            $table->json('foto_serah')->nullable();

            $table->date('tanggal_kembali')->nullable();
            $table->text('catatan_kembali')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_pemakai');
    }
};