<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aset_pemakai', function (Blueprint $table) {
            $table->string('nomor_pengembalian')->nullable()->unique()->after('nomor_serah_terima');
            $table->json('foto_kembali')->nullable()->after('catatan_kembali');
        });
    }

    public function down(): void
    {
        Schema::table('aset_pemakai', function (Blueprint $table) {
            $table->dropColumn(['nomor_pengembalian', 'foto_kembali']);
        });
    }
};