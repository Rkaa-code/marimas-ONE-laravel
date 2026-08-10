<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\JenisAset;
use App\Models\KelengkapanMaster;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AsetController extends Controller
{
    public function index(Request $request)
    {
        $aset = Aset::with(['jenis', 'supplier', 'pemakaiAktif.penerima'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('merek', 'like', '%' . $request->search . '%')
                        ->orWhere('tipe', 'like', '%' . $request->search . '%')
                        ->orWhere('serial_number', 'like', '%' . $request->search . '%')
                        ->orWhere('kode_aset', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->jenis_id, fn ($q) => $q->where('jenis_id', $request->jenis_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $jenisAset = JenisAset::orderBy('nama')->get();

        $statusCounts = Aset::when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('merek', 'like', '%' . $request->search . '%')
                        ->orWhere('tipe', 'like', '%' . $request->search . '%')
                        ->orWhere('serial_number', 'like', '%' . $request->search . '%')
                        ->orWhere('kode_aset', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->jenis_id, fn ($q) => $q->where('jenis_id', $request->jenis_id))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $statusCounts['semua'] = array_sum($statusCounts);

        return view('inventaris.aset.index', compact('aset', 'jenisAset', 'statusCounts'));
    }

    public function create()
    {
        $jenisAset = JenisAset::orderBy('nama')->get();
        $supplier = Supplier::orderBy('nama')->get();
        $kelengkapanMaster = KelengkapanMaster::orderBy('nama')->get();

        return view('inventaris.aset.create', compact('jenisAset', 'supplier', 'kelengkapanMaster'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('aset', 'public');
        }

        $aset = Aset::create($data);

        $aset->kelengkapan()->sync($request->input('kelengkapan', []));
        flash()->success('Aset berhasil ditambahkan.');
        return redirect()->route('inventaris.aset.index');
    }

    public function show(Aset $aset)
    {
        $aset->load(['jenis', 'supplier', 'kelengkapan']);

        return view('inventaris.aset.show', compact('aset'));
    }

    public function edit(Aset $aset)
    {
        $aset->load('kelengkapan');

        $jenisAset = JenisAset::orderBy('nama')->get();
        $supplier = Supplier::orderBy('nama')->get();
        $kelengkapanMaster = KelengkapanMaster::orderBy('nama')->get();

        return view('inventaris.aset.edit', compact('aset', 'jenisAset', 'supplier', 'kelengkapanMaster'));
    }

    public function update(Request $request, Aset $aset)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('foto')) {
            if ($aset->foto) {
                Storage::disk('public')->delete($aset->foto);
            }
            $data['foto'] = $request->file('foto')->store('aset', 'public');
        }

        $aset->update($data);

        $aset->kelengkapan()->sync($request->input('kelengkapan', []));

        return redirect()->route('inventaris.aset.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Aset $aset)
    {
        if ($aset->foto) {
            Storage::disk('public')->delete($aset->foto);
        }

        $aset->delete();

        return back()->with('success', 'Aset berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'jenis_id' => ['required', 'exists:jenis_aset,id'],
            'supplier_id' => ['nullable', 'exists:supplier,id'],
            'merek' => ['nullable', 'string', 'max:255'],
            'tipe' => ['nullable', 'string', 'max:255'],
            'warna' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'tanggal_garansi' => ['nullable', 'date'],
            'tanggal_pembelian' => ['nullable', 'date'],
            'no_surat_jalan' => ['nullable', 'string', 'max:255'],
            'no_good_receive' => ['nullable', 'string', 'max:255'],
            'perusahaan' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:tersedia,dipakai,menunggu_perbaikan,sedang_diperbaiki,rusak_berat,dijual'],
        ]);
    }
}