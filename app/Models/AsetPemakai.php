<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class AsetPenanganan extends Model
{
    use Auditable;

    protected $table = 'aset_penanganan';

    protected $fillable = [
        'aset_id',
        'status',
        'pelapor_user_id',
        'jenis_kerusakan',
        'keluhan',
        'foto_kerusakan',
        'tanggal_lapor',
        'diterima_oleh_user_id',
        'tanggal_terima',
        'tanggal_selesai',
        'harga_jasa',
        'biaya_komponen',
        'hasil',
        'no_struk',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
        'tanggal_terima' => 'date',
        'tanggal_selesai' => 'date',
        'harga_jasa' => 'decimal:2',
        'biaya_komponen' => 'decimal:2',
        'foto_kerusakan' => 'array',
    ];

    const STATUS_MENUNGGU_TERIMA = 'menunggu_terima';
    const STATUS_SEDANG_DIPERBAIKI = 'sedang_diperbaiki';
    const STATUS_BERHASIL_DIPERBAIKI = 'berhasil_diperbaiki';
    const STATUS_RUSAK_BERAT = 'rusak_berat';

    public function aset()
    {
        return $this->belongsTo(Aset::class);
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_user_id');
    }

    public function diterimaOleh()
    {
        return $this->belongsTo(User::class, 'diterima_oleh_user_id');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function totalBiaya(): float
    {
        return (float) $this->harga_jasa + (float) $this->biaya_komponen;
    }
}