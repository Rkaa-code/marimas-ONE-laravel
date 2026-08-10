<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetPemakai extends Model
{
    protected $table = 'aset_pemakai';

    protected $fillable = [
        'aset_id',
        'user_id',
        'diserahkan_oleh_user_id',
        'nomor_serah_terima',
        'tanggal_serah',
        'catatan_serah',
        'foto_serah',
        'nomor_pengembalian',
        'tanggal_kembali',
        'catatan_kembali',
        'foto_kembali',
    ];

    protected $casts = [
        'tanggal_serah' => 'date',
        'tanggal_kembali' => 'date',
        'foto_serah' => 'array',
        'foto_kembali' => 'array',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class);
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function diserahkanOleh()
    {
        return $this->belongsTo(User::class, 'diserahkan_oleh_user_id');
    }

    /** Masih dipegang (belum dikembalikan). */
    public function scopeAktif($query)
    {
        return $query->whereNull('tanggal_kembali');
    }
}