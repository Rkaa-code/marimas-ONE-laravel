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
        'tanggal_kembali',
        'catatan_kembali',
    ];

    protected $casts = [
        'tanggal_serah' => 'date',
        'tanggal_kembali' => 'date',
        'foto_serah' => 'array',
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