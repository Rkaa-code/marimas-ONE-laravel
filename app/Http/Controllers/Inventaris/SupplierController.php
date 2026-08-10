<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Supplier::withCount('aset')
            ->when($request->search, fn ($q) => $q->where('nama', 'like', '%' . $request->search . '%'))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('inventaris.master.supplier.index', compact('supplier'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ]);

        Supplier::create($data);

        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ]);

        $supplier->update($data);

        return back()->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->aset()->exists()) {
            return back()->with('error', 'Supplier tidak bisa dihapus karena masih dipakai aset.');
        }

        $supplier->delete();

        return back()->with('success', 'Supplier berhasil dihapus.');
    }
}
