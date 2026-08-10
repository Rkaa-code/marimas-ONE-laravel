<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $table = 'aset';

    protected $fillable = [
        'kode_aset',
        'jenis_id',
        'supplier_id',
        'merek',
        'tipe',
        'warna',
        'serial_number',
        'tanggal_garansi',
        'tanggal_pembelian',
        'no_surat_jalan',
        'no_good_receive',
        'perusahaan',
        'keterangan',
        'foto',
        'status',
    ];

    protected $casts = [
        'tanggal_garansi' => 'date',
        'tanggal_pembelian' => 'date',
    ];

    public function jenis()
    {
        return $this->belongsTo(JenisAset::class, 'jenis_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function kelengkapan()
    {
        return $this->belongsToMany(KelengkapanMaster::class, 'aset_kelengkapan', 'aset_id', 'kelengkapan_master_id');
    }

    public function riwayatPemakai()
    {
        return $this->hasMany(AsetPemakai::class)->latest('tanggal_serah');
    }

    /** Pemegang aktif sekarang (kalau status lagi 'dipakai'). */
    public function pemakaiAktif()
    {
        return $this->hasOne(AsetPemakai::class)->whereNull('tanggal_kembali')->latestOfMany('tanggal_serah');
    }

    public function penanganan()
    {
        return $this->hasMany(AsetPenanganan::class)->latest('tanggal_lapor');
    }

    /** Laporan kerusakan yang masih berjalan (belum berhasil diperbaiki / rusak berat). */
    public function penangananAktif()
    {
        return $this->hasOne(AsetPenanganan::class)
            ->whereIn('status', ['menunggu_terima', 'sedang_diperbaiki'])
            ->latestOfMany('tanggal_lapor');
    }
}