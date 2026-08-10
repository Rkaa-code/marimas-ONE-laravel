<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel notifikasi bawaan Laravel (Notifiable trait), dipakai buat
     * naruh notif "ada pembaruan aset" ke admin. Kolom persis skema
     * default Laravel biar bisa pakai fitur database notification
     * langsung (read/unread, markAsRead, dst) tanpa custom-custom lagi.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};