<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\AsetPemakai;
use App\Models\AsetPenanganan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AsetPemakaiController extends Controller
{
    /** Cari user buat typeahead di form serah-terima, difilter by tujuan. */
    public function cariPenerima(Request $request)
    {
        $data = $request->validate([
            'tujuan' => 'required|in:karyawan,cabang',
            'q' => 'nullable|string|max:100',
        ]);

        $roles = $data['tujuan'] === 'cabang' ? ['cabang'] : ['karyawan', 'hr', 'manajer'];

        $users = User::whereIn('role', $roles)
            ->when($data['q'] ?? null, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email', 'role']);

        return response()->json($users);
    }

    /**
     * Serahkan/pinjamkan aset ke karyawan (karyawan/hr/manajer) atau cabang.
     */
    public function store(Request $request, Aset $aset)
    {
        $data = $request->validate([
            'tujuan' => 'required|in:karyawan,cabang',
            'user_id' => 'required|exists:users,id',
            'tanggal_serah' => 'required|date',
            'catatan_serah' => 'nullable|string|max:1000',
            'foto_serah' => 'required|array|min:1|max:3',
            'foto_serah.*' => 'image|max:5120',
        ]);

        $penerima = User::findOrFail($data['user_id']);
        $roleValid = $data['tujuan'] === 'cabang'
            ? $penerima->hasRole('cabang')
            : $penerima->hasRole('karyawan', 'hr', 'manajer');

        abort_unless($roleValid, 422, 'Penerima tidak cocok dengan tujuan yang dipilih.');

        return DB::transaction(function () use ($request, $aset, $data) {
            // Lock baris aset biar gak ada 2 admin serahin aset yang sama bersamaan.
            $aset = Aset::whereKey($aset->id)->lockForUpdate()->firstOrFail();

            abort_if($aset->status !== 'tersedia', 422, 'Aset sedang tidak tersedia untuk diserahkan.');

            $fotoPaths = collect($request->file('foto_serah'))
                ->map(fn ($file) => $file->store('bukti-serah-terima', 'public'))
                ->all();

            $nomor = 'STJ-' . now()->format('Ymd') . '-' . str_pad(
                (AsetPemakai::whereDate('created_at', now())->count() + 1),
                4,
                '0',
                STR_PAD_LEFT
            );

            $pemakai = AsetPemakai::create([
                'aset_id' => $aset->id,
                'user_id' => $data['user_id'],
                'diserahkan_oleh_user_id' => Auth::id(),
                'nomor_serah_terima' => $nomor,
                'tanggal_serah' => $data['tanggal_serah'],
                'catatan_serah' => $data['catatan_serah'] ?? null,
                'foto_serah' => $fotoPaths,
            ]);

            $aset->update(['status' => 'dipakai']);
            flash()->success('Aset berhasil diserahkan ke ' . $pemakai->penerima->name . '.');
            return redirect()
                ->route('inventaris.aset.pemakai.struk', $pemakai)
                ->with('success', 'Aset berhasil diserahkan.');
        });
    }

    /** Tandai aset sudah dikembalikan, sekaligus opsi tandai rusak. */
    public function kembalikan(Request $request, AsetPemakai $pemakai)
    {
        $data = $request->validate([
            'kode_struk' => 'required|string',
            'tanggal_kembali' => 'required|date',
            'catatan_kembali' => 'nullable|string|max:1000',
            'foto_kembali' => 'required|array|min:1|max:3',
            'foto_kembali.*' => 'image|max:5120',
            'is_rusak' => 'nullable|boolean',
            'jenis_kerusakan' => 'required_if:is_rusak,1|nullable|in:hardware,software',
            'keluhan' => 'required_if:is_rusak,1|nullable|string|max:1000',
        ]);

        abort_if($pemakai->tanggal_kembali !== null, 422, 'Aset ini sudah tercatat dikembalikan.');
        abort_unless(
            strcasecmp(trim($data['kode_struk']), $pemakai->nomor_serah_terima) === 0,
            422,
            'Kode struk penerimaan tidak cocok dengan catatan serah-terima aset ini.'
        );

        $isRusak = $request->boolean('is_rusak');

        return DB::transaction(function () use ($request, $pemakai, $data, $isRusak) {
            $fotoPaths = collect($request->file('foto_kembali'))
                ->map(fn ($file) => $file->store('bukti-pengembalian', 'public'))
                ->all();

            $nomor = 'STP-' . now()->format('Ymd') . '-' . str_pad(
                (AsetPemakai::whereDate('created_at', now())->whereNotNull('nomor_pengembalian')->count() + 1),
                4,
                '0',
                STR_PAD_LEFT
            );

            $pemakai->update([
                'nomor_pengembalian' => $nomor,
                'tanggal_kembali' => $data['tanggal_kembali'],
                'catatan_kembali' => $data['catatan_kembali'] ?? null,
                'foto_kembali' => $fotoPaths,
            ]);

            if ($isRusak) {
                AsetPenanganan::create([
                    'aset_id' => $pemakai->aset_id,
                    'status' => AsetPenanganan::STATUS_MENUNGGU_TERIMA,
                    'pelapor_user_id' => $pemakai->user_id,
                    'jenis_kerusakan' => $data['jenis_kerusakan'],
                    'keluhan' => $data['keluhan'],
                    'tanggal_lapor' => $data['tanggal_kembali'],
                ]);

                $pemakai->aset()->update(['status' => 'menunggu_perbaikan']);
            } else {
                $pemakai->aset()->update(['status' => 'tersedia']);
            }

            return redirect()
                ->route('inventaris.aset.pemakai.struk-kembali', $pemakai)
                ->with('success', 'Aset berhasil ditandai dikembalikan.');
        });
    }

    /** Struk bukti pengembalian, siap diprint. */
    public function strukKembali(AsetPemakai $pemakai)
    {
        abort_if($pemakai->tanggal_kembali === null, 404);

        $pemakai->load(['aset', 'penerima']);

        return view('inventaris.aset.struk-kembali', ['pemakai' => $pemakai]);
    }

    /** Struk bukti serah terima, siap diprint. */
    public function struk(AsetPemakai $pemakai)
    {
        $pemakai->load(['aset', 'penerima', 'diserahkanOleh']);

        return view('inventaris.aset.struk', ['pemakai' => $pemakai]);
    }
}