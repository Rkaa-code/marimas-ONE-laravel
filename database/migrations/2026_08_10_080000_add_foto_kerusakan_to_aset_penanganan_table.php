<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aset_penanganan', function (Blueprint $table) {
            if (! Schema::hasColumn('aset_penanganan', 'foto_kerusakan')) {
                $table->json('foto_kerusakan')->nullable()->after('keluhan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aset_penanganan', function (Blueprint $table) {
            $table->dropColumn('foto_kerusakan');
        });
    }
};
