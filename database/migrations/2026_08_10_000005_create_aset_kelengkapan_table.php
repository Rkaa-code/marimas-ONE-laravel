<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_kelengkapan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_id')->constrained('aset')->cascadeOnDelete();
            $table->foreignId('kelengkapan_master_id')->constrained('kelengkapan_master');
            $table->timestamps();

            $table->unique(['aset_id', 'kelengkapan_master_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_kelengkapan');
    }
};
