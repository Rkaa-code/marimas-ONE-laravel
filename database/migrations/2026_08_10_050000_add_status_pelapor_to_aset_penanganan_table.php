<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lengkapi tabel aset_penanganan biar bisa nampung alur:
     * lapor (pemakai) -> terima (admin/IT) -> proses -> selesai (berhasil/rusak berat).
     */
    public function up(): void
    {
        Schema::table('aset_penanganan', function (Blueprint $table) {
            $table->string('status')->default('menunggu_terima')->after('aset_id');

            $table->foreignId('pelapor_user_id')->nullable()->after('status')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('diterima_oleh_user_id')->nullable()->after('tanggal_lapor')
                ->constrained('users')->nullOnDelete();
            $table->date('tanggal_terima')->nullable()->after('diterima_oleh_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('aset_penanganan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pelapor_user_id');
            $table->dropConstrainedForeignId('diterima_oleh_user_id');
            $table->dropColumn(['status', 'tanggal_terima']);
        });
    }
};
