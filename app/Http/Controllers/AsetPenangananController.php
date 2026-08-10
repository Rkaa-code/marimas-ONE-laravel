<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\AsetPenanganan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsetPenangananController extends Controller
{
    /** Daftar laporan penanganan, dikelompokkan per tab status. */
    public function index(Request $request)
    {
        $activeStatus = $request->get('status', AsetPenanganan::STATUS_MENUNGGU_TERIMA);

        $statusCounts = AsetPenanganan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $penanganan = AsetPenanganan::with(['aset', 'pelapor'])
            ->status($activeStatus)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('keluhan', 'like', "%{$search}%")
                        ->orWhereHas('aset', fn ($a) => $a->where('kode_aset', 'like', "%{$search}%"))
                        ->orWhereHas('pelapor', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('tanggal_lapor')
            ->paginate(10)
            ->withQueryString();

        return view('inventaris.penanganan-aset.index', [
            'penanganan' => $penanganan,
            'activeStatus' => $activeStatus,
            'statusCounts' => $statusCounts,
        ]);
    }

    /** Detail satu laporan penanganan (dibuka lewat ikon mata di kolom Aksi). */
    public function show(AsetPenanganan $penanganan)
    {
        $penanganan->load(['aset.jenis', 'pelapor', 'diterimaOleh']);

        return view('inventaris.penanganan-aset.show', [
            'penanganan' => $penanganan,
        ]);
    }

    /**
     * Lapor kerusakan dari halaman detail aset (tombol "Lapor Rusak"
     * di samping "Tandai Dikembalikan" saat aset sedang dipakai).
     */
    public function store(Request $request, Aset $aset)
    {
        $data = $request->validate([
            'jenis_kerusakan' => 'required|in:software,hardware',
            'keluhan' => 'required|string|max:1000',
        ]);

        abort_unless($aset->status === 'dipakai' && $aset->pemakaiAktif, 422, 'Aset ini sedang tidak dipakai siapa pun.');

        DB::transaction(function () use ($aset, $data) {
            AsetPenanganan::create([
                'aset_id' => $aset->id,
                'status' => AsetPenanganan::STATUS_MENUNGGU_TERIMA,
                'pelapor_user_id' => $aset->pemakaiAktif->user_id,
                'jenis_kerusakan' => $data['jenis_kerusakan'],
                'keluhan' => $data['keluhan'],
                'tanggal_lapor' => now(),
            ]);

            // Aset ditarik balik ke IT bareng laporan kerusakannya.
            $aset->pemakaiAktif->update([
                'tanggal_kembali' => now(),
                'catatan_kembali' => 'Dikembalikan karena laporan kerusakan.',
            ]);

            $aset->update(['status' => 'menunggu_perbaikan']);
        });

        return back()->with('success', 'Laporan kerusakan berhasil dikirim ke tim IT.');
    }

    /** Admin/IT menerima laporan -> mulai diperbaiki. */
    public function terima(AsetPenanganan $penanganan)
    {
        abort_if($penanganan->status !== AsetPenanganan::STATUS_MENUNGGU_TERIMA, 422, 'Laporan ini sudah diproses.');

        DB::transaction(function () use ($penanganan) {
            $penanganan->update([
                'status' => AsetPenanganan::STATUS_SEDANG_DIPERBAIKI,
                'diterima_oleh_user_id' => Auth::id(),
                'tanggal_terima' => now(),
            ]);

            $penanganan->aset()->update(['status' => 'sedang_diperbaiki']);
        });

        return back()->with('success', 'Laporan diterima, aset masuk status sedang diperbaiki.');
    }

    /** Tandai laporan selesai: berhasil diperbaiki atau rusak berat. */
    public function selesai(Request $request, AsetPenanganan $penanganan)
    {
        abort_if($penanganan->status !== AsetPenanganan::STATUS_SEDANG_DIPERBAIKI, 422, 'Laporan ini belum bisa diselesaikan.');

        $data = $request->validate([
            'hasil' => 'required|in:berhasil_diperbaiki,rusak_berat',
            'harga_jasa' => 'nullable|numeric|min:0',
            'biaya_komponen' => 'nullable|numeric|min:0',
            'no_struk' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($penanganan, $data) {
            $penanganan->update([
                'status' => $data['hasil'],
                'hasil' => $data['hasil'],
                'tanggal_selesai' => now(),
                'harga_jasa' => $data['harga_jasa'] ?? null,
                'biaya_komponen' => $data['biaya_komponen'] ?? null,
                'no_struk' => $data['no_struk'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $penanganan->aset()->update([
                'status' => $data['hasil'] === 'berhasil_diperbaiki' ? 'tersedia' : 'rusak_berat',
            ]);
        });

        return redirect()
            ->route('inventaris.penanganan-aset.index', ['status' => $data['hasil']])
            ->with('success', 'Laporan penanganan berhasil diperbarui.');
    }
}
