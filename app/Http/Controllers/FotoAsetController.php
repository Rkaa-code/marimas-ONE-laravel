<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use Illuminate\Http\Request;

class FotoAsetController extends Controller
{
    /** Galeri foto seluruh aset — cuma yang punya foto tersimpan. */
    public function index(Request $request)
    {
        $aset = Aset::with('jenis')
            ->whereNotNull('foto')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $search = $request->get('search');
                $q->where('kode_aset', 'like', "%{$search}%")
                    ->orWhere('merek', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%");
            }))
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('inventaris.foto-aset.index', [
            'aset' => $aset,
        ]);
    }
}
