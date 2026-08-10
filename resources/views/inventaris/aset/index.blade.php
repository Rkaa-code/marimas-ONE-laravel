@extends('layouts.app')

@section('title', 'Aset')

@php
    $statusLabel = [
        'tersedia' => 'Tersedia',
        'dipakai' => 'Dipakai',
        'menunggu_perbaikan' => 'Menunggu Perbaikan',
        'sedang_diperbaiki' => 'Sedang Diperbaiki',
        'rusak_berat' => 'Rusak Berat',
        'dijual' => 'Dijual',
    ];
    $statusColor = [
        'tersedia' => 'bg-emerald-50 text-emerald-700',
        'dipakai' => 'bg-amber-50 text-amber-700',
        'menunggu_perbaikan' => 'bg-yellow-50 text-yellow-700',
        'sedang_diperbaiki' => 'bg-sky-50 text-sky-700',
        'rusak_berat' => 'bg-red-100 text-red-800',
        'dijual' => 'bg-purple-50 text-purple-700',
    ];
@endphp

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Aset</h1>
            <p class="text-sm text-slate-500">Daftar seluruh aset perusahaan.</p>
        </div>
        <a href="{{ route('inventaris.aset.create') }}"
           class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            + Tambah Aset
        </a>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari merek, tipe, serial number..."
               class="w-full max-w-sm rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">

        <select name="jenis_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            <option value="">Semua Jenis</option>
            @foreach ($jenisAset as $jenis)
                <option value="{{ $jenis->id }}" @selected(request('jenis_id') == $jenis->id)>{{ $jenis->nama }}</option>
            @endforeach
        </select>

        <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            <option value="">Semua Status</option>
            @foreach ($statusLabel as $value => $label)
                <option value="{{ $value }}" @selected(request('status') == $value)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Filter
        </button>

        @if (request()->hasAny(['search', 'jenis_id', 'status']))
            <a href="{{ route('inventaris.aset.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-700">
                Reset
            </a>
        @endif
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Aset</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3">Serial Number</th>
                    <th class="px-4 py-3">Supplier</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($aset as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">{{ $item->merek ?: '-' }} {{ $item->tipe }}</div>
                            <div class="text-xs text-slate-500">{{ $item->warna }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $item->jenis?->nama ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->serial_number ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->supplier?->nama ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap {{ $statusColor[$item->status] }}">
                                {{ $statusLabel[$item->status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('inventaris.aset.show', $item) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Detail</a>
                            <a href="{{ route('inventaris.aset.edit', $item) }}" class="ml-3 text-sm font-medium text-slate-600 hover:text-slate-900">Edit</a>
                            <form action="{{ route('inventaris.aset.destroy', $item) }}" method="POST"
                                  class="inline" onsubmit="return confirm('Hapus aset ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 text-sm font-medium text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada aset.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $aset->links() }}
    </div>
@endsection
