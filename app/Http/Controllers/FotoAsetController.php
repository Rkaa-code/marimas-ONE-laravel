<?php

namespace App\Http\Controllers;

use App\Models\AsetPemakai;
use App\Models\AsetPenanganan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoAsetController extends Controller
{
    /** Galeri bukti foto: peminjaman, pengembalian, laporan rusak, dan gabungan semuanya. */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'peminjaman');
        $tab = in_array($tab, ['peminjaman', 'pengembalian', 'rusak', 'semua'], true) ? $tab : 'peminjaman';

        $search = $request->get('search');

        $counts = [
            'peminjaman' => AsetPemakai::whereNotNull('foto_serah')->count(),
            'pengembalian' => AsetPemakai::whereNotNull('foto_kembali')->count(),
            'rusak' => AsetPenanganan::whereNotNull('foto_kerusakan')->count(),
        ];

        $data = match ($tab) {
            'pengembalian' => AsetPemakai::with(['aset', 'penerima'])
                ->whereNotNull('foto_kembali')
                ->when($search, fn ($q) => $q->whereHas('aset', fn ($a) => $a->where('kode_aset', 'like', "%{$search}%")
                    ->orWhere('merek', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")))
                ->latest('tanggal_kembali')
                ->paginate(10)
                ->withQueryString(),
            'rusak' => AsetPenanganan::with(['aset', 'pelapor'])
                ->whereNotNull('foto_kerusakan')
                ->when($search, fn ($q) => $q->whereHas('aset', fn ($a) => $a->where('kode_aset', 'like', "%{$search}%")
                    ->orWhere('merek', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")))
                ->latest('tanggal_lapor')
                ->paginate(10)
                ->withQueryString(),
            'semua' => $this->semua($search),
            default => AsetPemakai::with(['aset', 'penerima'])
                ->whereNotNull('foto_serah')
                ->when($search, fn ($q) => $q->whereHas('aset', fn ($a) => $a->where('kode_aset', 'like', "%{$search}%")
                    ->orWhere('merek', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")))
                ->latest('tanggal_serah')
                ->paginate(10)
                ->withQueryString(),
        };

        return view('inventaris.foto-aset.index', [
            'tab' => $tab,
            'counts' => $counts,
            'data' => $data,
        ]);
    }

    /**
     * Gabungan semua kategori foto (peminjaman, pengembalian, rusak) jadi satu
     * daftar, diurut dari yang paling baru, masing-masing dikasih tag status.
     */
    protected function semua(?string $search)
    {
        $peminjaman = AsetPemakai::with(['aset', 'penerima'])
            ->whereNotNull('foto_serah')
            ->when($search, fn ($q) => $q->whereHas('aset', fn ($a) => $a->where('kode_aset', 'like', "%{$search}%")
                ->orWhere('merek', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%")))
            ->get()
            ->map(fn ($item) => [
                'status' => 'peminjaman',
                'aset' => $item->aset,
                'nama' => $item->penerima?->name,
                'tanggal' => $item->tanggal_serah,
                'jumlah_foto' => count($item->foto_serah ?? []),
                'fotos' => collect($item->foto_serah ?? [])->map(fn ($p) => Storage::url($p))->all(),
            ]);

        $pengembalian = AsetPemakai::with(['aset', 'penerima'])
            ->whereNotNull('foto_kembali')
            ->when($search, fn ($q) => $q->whereHas('aset', fn ($a) => $a->where('kode_aset', 'like', "%{$search}%")
                ->orWhere('merek', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%")))
            ->get()
            ->map(fn ($item) => [
                'status' => 'pengembalian',
                'aset' => $item->aset,
                'nama' => $item->penerima?->name,
                'tanggal' => $item->tanggal_kembali,
                'jumlah_foto' => count($item->foto_kembali ?? []),
                'fotos' => collect($item->foto_kembali ?? [])->map(fn ($p) => Storage::url($p))->all(),
            ]);

        $rusak = AsetPenanganan::with(['aset', 'pelapor'])
            ->whereNotNull('foto_kerusakan')
            ->when($search, fn ($q) => $q->whereHas('aset', fn ($a) => $a->where('kode_aset', 'like', "%{$search}%")
                ->orWhere('merek', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%")))
            ->get()
            ->map(fn ($item) => [
                'status' => 'rusak',
                'aset' => $item->aset,
                'nama' => $item->pelapor?->name,
                'tanggal' => $item->tanggal_lapor,
                'jumlah_foto' => count($item->foto_kerusakan ?? []),
                'fotos' => collect($item->foto_kerusakan ?? [])->map(fn ($p) => Storage::url($p))->all(),
            ]);

        $semua = $peminjaman->concat($pengembalian)->concat($rusak)
            ->sortByDesc(fn ($row) => $row['tanggal']?->timestamp ?? 0)
            ->values();

        // Pagination manual karena datanya gabungan dari 3 query berbeda.
        $page = (int) request('page', 1);
        $perPage = 10;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $semua->forPage($page, $perPage),
            $semua->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
