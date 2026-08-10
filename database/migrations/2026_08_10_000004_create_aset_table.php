<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique();
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

        // Auto-generate kode_aset kalau belum diisi manual, format:
        // IT-{tahun}-{kode jenis aset}-{urutan 3 digit}, urutan reset per
        // jenis per tahun. Pake pg_advisory_xact_lock biar race-safe kalau
        // ada 2 insert bareng buat jenis+tahun yang sama.
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION generate_kode_aset() RETURNS TRIGGER AS $$
            DECLARE
                v_kode_jenis TEXT;
                v_tahun TEXT := to_char(now(), 'YYYY');
                v_prefix TEXT;
                v_next_seq INT;
            BEGIN
                IF NEW.kode_aset IS NOT NULL AND NEW.kode_aset <> '' THEN
                    RETURN NEW;
                END IF;

                SELECT UPPER(REGEXP_REPLACE(nama, '[^A-Za-z0-9]', '', 'g')) INTO v_kode_jenis
                FROM jenis_aset WHERE id = NEW.jenis_id;

                IF v_kode_jenis IS NULL OR v_kode_jenis = '' THEN
                    RAISE EXCEPTION 'Jenis aset % tidak ditemukan / nama kosong, tidak bisa generate kode_aset', NEW.jenis_id;
                END IF;

                v_prefix := 'IT-' || v_tahun || '-' || v_kode_jenis || '-';

                -- Lock per kombinasi jenis+tahun biar insert bareng gak dapet urutan sama
                PERFORM pg_advisory_xact_lock(hashtext(v_prefix));

                SELECT COALESCE(MAX(CAST(SUBSTRING(kode_aset FROM '[0-9]+$') AS INT)), 0) + 1
                INTO v_next_seq
                FROM aset
                WHERE kode_aset LIKE v_prefix || '%';

                NEW.kode_aset := v_prefix || LPAD(v_next_seq::TEXT, 3, '0');

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_generate_kode_aset
                BEFORE INSERT ON aset
                FOR EACH ROW
                EXECUTE FUNCTION generate_kode_aset();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_generate_kode_aset ON aset');
        DB::unprepared('DROP FUNCTION IF EXISTS generate_kode_aset()');

        Schema::dropIfExists('aset');
    }
};