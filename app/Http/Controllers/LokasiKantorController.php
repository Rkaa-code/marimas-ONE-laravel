<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LokasiKantor;

class LokasiKantorController extends Controller
{
    public function index()
    {
        $cabangs = LokasiKantor::all()->map(function ($c) {
            return [
                'id' => $c->id,
                'nama' => $c->nama,
                'alamat' => $c->alamat,
                'telepon' => $c->telepon,
                'link' => $c->link,
            ];
        });

        // Sesuaikan dengan sistem role/auth kamu.
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        return view('cabang.index', compact('cabangs', 'isAdmin'));
    }

    public function create()
    {
        return view('cabang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'link' => ['nullable', 'url', 'max:500'],
        ]);

        $cabang = LokasiKantor::create($validated);

        return response()->json([
            'message' => 'Cabang berhasil ditambahkan.',
            'cabang' => [
                'id' => $cabang->id,
                'nama' => $cabang->nama,
                'alamat' => $cabang->alamat,
                'telepon' => $cabang->telepon,
                'link' => $cabang->link,
            ],
        ], 201);
    }
}