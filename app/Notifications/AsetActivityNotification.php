<?php

namespace App\Notifications;

use App\Models\Aset;
use Illuminate\Notifications\Notification;

/**
 * Notif buat admin tiap kali ada pembaruan info aset: aset baru, edit data,
 * ganti status (diserahkan, dikembalikan, lapor rusak, diperbaiki, dst),
 * atau aset dihapus. Isi/level notif dibikin sesuai kondisi yang trigger
 * lewat App\Models\Concerns\NotifiesAdmin.
 *
 * Sengaja gak implements ShouldQueue: harus langsung kekirim pas request
 * jalan tanpa gantung ke queue worker yang belum tentu nyala.
 */
class AsetActivityNotification extends Notification
{
    /**
     * @param  'info'|'success'|'warning'|'danger'  $level  Warna/ikon di UI notif.
     */
    public function __construct(
        public Aset $aset,
        public string $title,
        public string $message,
        public string $level = 'info',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'aset_id' => $this->aset->id,
            'kode_aset' => $this->aset->kode_aset,
            'title' => $this->title,
            'message' => $this->message,
            'level' => $this->level,
            'url' => route('inventaris.aset.show', $this->aset),
        ];
    }
}