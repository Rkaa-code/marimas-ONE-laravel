<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\JenisAset;
use Illuminate\Http\Request;

class JenisAsetController extends Controller
{
    public function index(Request $request)
    {
        $jenisAset = JenisAset::withCount('aset')
            ->when($request->search, fn ($q) => $q->where('nama', 'like', '%' . $request->search . '%'))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('inventaris.master.jenis-aset.index', compact('jenisAset'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:jenis_aset,kode'],
        ]);

        $data['kode'] = strtoupper($data['kode']);

        JenisAset::create($data);

        return back()->with('success', 'Jenis aset berhasil ditambahkan.');
    }

    public function update(Request $request, JenisAset $jenisAset)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:jenis_aset,kode,' . $jenisAset->id],
        ]);

        $data['kode'] = strtoupper($data['kode']);

        $jenisAset->update($data);

        return back()->with('success', 'Jenis aset berhasil diperbarui.');
    }

    public function destroy(JenisAset $jenisAset)
    {
        if ($jenisAset->aset()->exists()) {
            return back()->with('error', 'Jenis aset tidak bisa dihapus karena masih dipakai aset.');
        }

        $jenisAset->delete();

        return back()->with('success', 'Jenis aset berhasil dihapus.');
    }
}