<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LokasiCabang extends Model
{
    protected $table = 'lokasi_cabang';

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'link'
    ];

    public function pekerja(): HasMany
    {
        return $this->hasMany(Pekerja::class, 'lokasi_kantor_id');
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'lokasi_kantor_id');
    }
}