<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\KelengkapanMaster;
use Illuminate\Http\Request;

class KelengkapanMasterController extends Controller
{
    public function index(Request $request)
    {
        $kelengkapan = KelengkapanMaster::withCount('aset')
            ->when($request->search, fn ($q) => $q->where('nama', 'like', '%' . $request->search . '%'))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('inventaris.master.kelengkapan.index', compact('kelengkapan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        KelengkapanMaster::create($data);

        return back()->with('success', 'Kelengkapan berhasil ditambahkan.');
    }

    public function update(Request $request, KelengkapanMaster $kelengkapan)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $kelengkapan->update($data);

        return back()->with('success', 'Kelengkapan berhasil diperbarui.');
    }

    public function destroy(KelengkapanMaster $kelengkapan)
    {
        if ($kelengkapan->aset()->exists()) {
            return back()->with('error', 'Kelengkapan tidak bisa dihapus karena masih dipakai aset.');
        }

        $kelengkapan->delete();

        return back()->with('success', 'Kelengkapan berhasil dihapus.');
    }
}
