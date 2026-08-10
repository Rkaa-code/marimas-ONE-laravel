<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelengkapanMaster extends Model
{
    protected $table = 'kelengkapan_master';

    protected $fillable = [
        'nama',
    ];

    public function aset()
    {
        return $this->belongsToMany(Aset::class, 'aset_kelengkapan', 'kelengkapan_master_id', 'aset_id');
    }
}
