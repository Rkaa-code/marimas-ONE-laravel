<?php

namespace App\Models\Concerns;

use App\Models\User;
use App\Notifications\AsetActivityNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Tempel di model Aset: tiap kali ada aset baru, info-nya diubah, status-nya
 * ganti (diserahkan, dikembalikan, lapor rusak, diperbaiki, dst), atau
 * dihapus, semua admin otomatis dapet notif sesuai kondisi yang terjadi.
 *
 * Pola event-nya sama kayak trait Auditable, cuma di sini yang dicatat
 * bukan audit log, tapi notif buat admin.
 */
trait NotifiesAdmin
{
    /** Label field aset dalam Bahasa Indonesia, buat pesan "info aset diubah". */
    protected static array $notifiableFieldLabels = [
        'jenis_id' => 'jenis aset',
        'supplier_id' => 'supplier',
        'merek' => 'merek',
        'tipe' => 'tipe',
        'warna' => 'warna',
        'serial_number' => 'nomor seri',
        'tanggal_garansi' => 'tanggal garansi',
        'tanggal_pembelian' => 'tanggal pembelian',
        'no_surat_jalan' => 'nomor surat jalan',
        'no_good_receive' => 'nomor good receive',
        'perusahaan' => 'perusahaan',
        'keterangan' => 'keterangan',
        'foto' => 'foto',
    ];

    public static function bootNotifiesAdmin(): void
    {
        static::created(function ($aset) {
            // kode_aset sering di-generate lewat trigger DB pas insert,
            // jadi tarik ulang biar pesannya gak kosong.
            $kode = $aset->kode_aset ?: optional($aset->fresh())->kode_aset;

            $aset->notifyAdmins(
                title: 'Aset Baru Ditambahkan',
                message: "Aset baru {$kode} ({$aset->merek} {$aset->tipe}) telah ditambahkan ke inventaris.",
                level: 'success',
            );
        });

        static::updated(function ($aset) {
            $changes = $aset->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            if (array_key_exists('status', $changes)) {
                $aset->notifyStatusChange($aset->getOriginal('status'), $changes['status']);

                unset($changes['status']);
            }

            if (! empty($changes)) {
                $labels = collect($changes)->keys()
                    ->map(fn ($field) => static::$notifiableFieldLabels[$field] ?? $field)
                    ->implode(', ');

                $aset->notifyAdmins(
                    title: 'Informasi Aset Diperbarui',
                    message: "Data aset {$aset->kode_aset} diperbarui: {$labels}.",
                    level: 'info',
                );
            }
        });

        static::deleted(function ($aset) {
            $aset->notifyAdmins(
                title: 'Aset Dihapus',
                message: "Aset {$aset->kode_aset} ({$aset->merek} {$aset->tipe}) telah dihapus dari inventaris.",
                level: 'danger',
            );
        });
    }

    /** Bikin pesan notif yang pas sesuai perpindahan status aset. */
    protected function notifyStatusChange(?string $from, string $to): void
    {
        [$title, $message, $level] = match ($to) {
            'dipakai' => [
                'Aset Diserahkan',
                "Aset {$this->kode_aset} telah diserahkan ke " . ($this->pemakaiAktif?->penerima?->name ?? 'pengguna') . '.',
                'info',
            ],
            'tersedia' => $from === 'sedang_diperbaiki'
                ? [
                    'Perbaikan Berhasil',
                    "Aset {$this->kode_aset} berhasil diperbaiki dan kini tersedia kembali.",
                    'success',
                ]
                : [
                    'Aset Dikembalikan',
                    "Aset {$this->kode_aset} telah dikembalikan dan berstatus tersedia.",
                    'success',
                ],
            'menunggu_perbaikan' => [
                'Laporan Kerusakan Baru',
                "Aset {$this->kode_aset} dilaporkan rusak dan menunggu diterima tim IT. " .
                    ($this->penangananAktif?->keluhan ? "Keluhan: {$this->penangananAktif->keluhan}." : ''),
                'warning',
            ],
            'sedang_diperbaiki' => [
                'Perbaikan Dimulai',
                "Aset {$this->kode_aset} diterima tim IT dan mulai diperbaiki.",
                'info',
            ],
            'rusak_berat' => [
                'Aset Rusak Berat',
                "Aset {$this->kode_aset} dinyatakan rusak berat dan tidak dapat diperbaiki.",
                'danger',
            ],
            'dijual' => [
                'Aset Dijual/Write-off',
                "Aset {$this->kode_aset} telah ditandai dijual/write-off.",
                'warning',
            ],
            default => [
                'Status Aset Berubah',
                "Status aset {$this->kode_aset} berubah menjadi {$to}.",
                'info',
            ],
        };

        $this->notifyAdmins(title: $title, message: trim($message), level: $level);
    }

    /** Kirim notif ke semua user berrole admin (kecuali yang lagi login, biar gak notif diri sendiri). */
    protected function notifyAdmins(string $title, string $message, string $level = 'info'): void
    {
        $admins = User::where('role', 'admin')
            ->when(auth()->id(), fn ($q, $userId) => $q->where('id', '!=', $userId))
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new AsetActivityNotification($this, $title, $message, $level));
    }
}