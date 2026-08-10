<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Notif pembaruan aset buat admin (aset baru, edit info, ganti status,
 * dihapus, dst). Notifnya sendiri dibuat otomatis lewat
 * App\Models\Concerns\NotifiesAdmin, controller ini cuma nampilin &
 * ngurus tandai-dibaca-nya.
 */
class NotificationController extends Controller
{
    /** Halaman daftar notif lengkap. */
    public function index()
    {
        $notifikasi = Auth::user()
            ->notifications()
            ->paginate(20);

        return view('notifikasi.index', compact('notifikasi'));
    }

    /** Dipanggil polling dari lonceng notif di header (jumlah belum dibaca + 8 terbaru). */
    public function terbaru()
    {
        $user = Auth::user();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifikasi' => $user->notifications()
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn ($n) => [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? '',
                    'message' => $n->data['message'] ?? '',
                    'level' => $n->data['level'] ?? 'info',
                    'url' => $n->data['url'] ?? null,
                    'read' => $n->read_at !== null,
                    'waktu' => $n->created_at->diffForHumans(),
                ]),
        ]);
    }

    /** Tandai satu notif dibaca (dipencet dari dropdown/list), lalu lempar ke url tujuannya. */
    public function baca(Request $request, string $notification)
    {
        $item = Auth::user()->notifications()->whereKey($notification)->firstOrFail();

        $item->markAsRead();

        $url = $item->data['url'] ?? null;

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return $url ? redirect($url) : back();
    }

    /** Tandai semua notif dibaca. */
    public function bacaSemua()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
