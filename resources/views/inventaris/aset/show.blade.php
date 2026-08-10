@extends('layouts.app')

@section('title', 'Detail Aset')

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
        'tersedia' => 'bg-green-100 text-green-800',
        'dipakai' => 'bg-blue-100 text-blue-800',
        'menunggu_perbaikan' => 'bg-yellow-100 text-yellow-800',
        'sedang_diperbaiki' => 'bg-orange-100 text-orange-800',
        'rusak_berat' => 'bg-red-100 text-red-800',
        'dijual' => 'bg-slate-200 text-slate-700',
    ];
@endphp

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ $aset->merek }} {{ $aset->tipe }}</h1>
            <span class="mt-1 inline-block rounded-full px-2 py-1 text-xs font-medium {{ $statusColor[$aset->status] }}">
                {{ $statusLabel[$aset->status] }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('inventaris.aset.edit', $aset) }}"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a>
            <a href="{{ route('inventaris.aset.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="md:col-span-2 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Informasi Aset</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slate-500">Jenis</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->jenis?->nama ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Warna</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->warna ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Serial Number</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->serial_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Supplier</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->supplier?->nama ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Tanggal Pembelian</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->tanggal_pembelian?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Tanggal Garansi</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->tanggal_garansi?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">No Surat Jalan</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->no_surat_jalan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">No Good Receive</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->no_good_receive ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Perusahaan</dt>
                        <dd class="font-medium text-slate-900">{{ $aset->perusahaan ?? '-' }}</dd>
                    </div>
                </dl>

                @if ($aset->keterangan)
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <dt class="text-slate-500 text-sm">Keterangan</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $aset->keterangan }}</dd>
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Kelengkapan</h2>
                @if ($aset->kelengkapan->isEmpty())
                    <p class="text-sm text-slate-400">Tidak ada kelengkapan tercatat.</p>
                @else
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($aset->kelengkapan as $item)
                            <li class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">{{ $item->nama }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div>
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Foto</h2>
                @if ($aset->foto)
                    <img src="{{ Storage::url($aset->foto) }}" alt="Foto aset" class="w-full rounded-lg object-cover">
                @else
                    <p class="text-sm text-slate-400">Belum ada foto.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
