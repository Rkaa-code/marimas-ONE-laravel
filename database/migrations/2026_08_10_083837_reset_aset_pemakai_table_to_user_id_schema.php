<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel aset_pemakai di production masih pakai skema lama (pekerja_id,
     * nomor_penerimaan, status, requested_by_user_id, dst) dari sistem
     * approval lama, padahal model & controller sekarang sudah pakai skema
     * baru (user_id, nomor_serah_terima, dst). Migration create aslinya
     * sudah ditulis ulang tapi gak jalan lagi karena sudah tercatat "Ran".
     *
     * Datanya masih data testing, jadi kita drop & bikin ulang sesuai
     * skema yang sekarang dipakai model.
     */
    public function up(): void
    {
        Schema::dropIfExists('aset_pemakai');

        Schema::create('aset_pemakai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')->constrained('aset')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('diserahkan_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('nomor_serah_terima')->unique();
            $table->date('tanggal_serah');
            $table->text('catatan_serah')->nullable();
            $table->json('foto_serah')->nullable();

            $table->string('nomor_pengembalian')->nullable()->unique();
            $table->date('tanggal_kembali')->nullable();
            $table->text('catatan_kembali')->nullable();
            $table->json('foto_kembali')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_pemakai');
    }
};