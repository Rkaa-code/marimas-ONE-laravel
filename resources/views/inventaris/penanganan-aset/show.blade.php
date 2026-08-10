@extends('layouts.app')

@section('title', 'Detail Penanganan Aset')

@php
    $statusLabel = [
        'menunggu_terima' => 'Menunggu Terima',
        'sedang_diperbaiki' => 'Sedang Diperbaiki',
        'berhasil_diperbaiki' => 'Berhasil Diperbaiki',
        'rusak_berat' => 'Rusak Berat',
    ];
    $statusColor = [
        'menunggu_terima' => 'bg-amber-50 text-amber-700',
        'sedang_diperbaiki' => 'bg-sky-50 text-sky-700',
        'berhasil_diperbaiki' => 'bg-emerald-50 text-emerald-700',
        'rusak_berat' => 'bg-red-100 text-red-800',
    ];
@endphp

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <span class="inline-block rounded-full bg-slate-100 px-2.5 py-1 text-xs font-mono font-medium text-slate-700">
                {{ $penanganan->aset->kode_aset ?? '-' }}
            </span>
            <h1 class="mt-1.5 text-xl font-semibold text-slate-900">Laporan Kerusakan #{{ $penanganan->id }}</h1>
            <span class="mt-1 inline-block rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap {{ $statusColor[$penanganan->status] }}">
                {{ $statusLabel[$penanganan->status] }}
            </span>
        </div>
        <a href="{{ route('inventaris.penanganan-aset.index', ['status' => $penanganan->status]) }}"
           class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Kembali</a>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="md:col-span-2 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Detail Laporan</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slate-500">Aset</dt>
                        <dd class="font-medium text-slate-900">
                            {{ $penanganan->aset->merek ?? '' }} {{ $penanganan->aset->tipe ?? '' }}
                            ({{ $penanganan->aset->jenis?->nama ?? '-' }})
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Jenis Kerusakan</dt>
                        <dd class="font-medium text-slate-900 capitalize">{{ $penanganan->jenis_kerusakan }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Pelapor</dt>
                        <dd class="font-medium text-slate-900">{{ $penanganan->pelapor->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Tanggal Lapor</dt>
                        <dd class="font-medium text-slate-900">{{ $penanganan->tanggal_lapor?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Diterima Oleh</dt>
                        <dd class="font-medium text-slate-900">{{ $penanganan->diterimaOleh->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Tanggal Terima</dt>
                        <dd class="font-medium text-slate-900">{{ $penanganan->tanggal_terima?->format('d M Y') ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <dt class="text-slate-500 text-sm">Keluhan</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $penanganan->keluhan }}</dd>
                </div>
            </div>

            @if (in_array($penanganan->status, ['berhasil_diperbaiki', 'rusak_berat']))
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900">Hasil Penanganan</h2>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Tanggal Selesai</dt>
                            <dd class="font-medium text-slate-900">{{ $penanganan->tanggal_selesai?->format('d M Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">No. Struk</dt>
                            <dd class="font-medium text-slate-900">{{ $penanganan->no_struk ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Harga Jasa</dt>
                            <dd class="font-medium text-slate-900">Rp {{ number_format($penanganan->harga_jasa ?? 0, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Biaya Komponen</dt>
                            <dd class="font-medium text-slate-900">Rp {{ number_format($penanganan->biaya_komponen ?? 0, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                    @if ($penanganan->catatan)
                        <div class="mt-4 border-t border-slate-100 pt-4">
                            <dt class="text-slate-500 text-sm">Catatan</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $penanganan->catatan }}</dd>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($penanganan->status === 'menunggu_terima')
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-6">
                    <h2 class="mb-2 text-sm font-semibold text-amber-900">Belum Diterima</h2>
                    <p class="mb-4 text-sm text-amber-700">Terima laporan ini untuk mulai memproses perbaikan.</p>
                    <form action="{{ route('inventaris.penanganan-aset.terima', $penanganan) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                            Terima Laporan
                        </button>
                    </form>
                </div>
            @elseif ($penanganan->status === 'sedang_diperbaiki')
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900">Selesaikan Penanganan</h2>
                    <form action="{{ route('inventaris.penanganan-aset.selesai', $penanganan) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Hasil</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 py-2 text-sm font-medium text-slate-700 has-[:checked]:border-slate-900 has-[:checked]:bg-slate-900 has-[:checked]:text-white">
                                    <input type="radio" name="hasil" value="berhasil_diperbaiki" class="hidden" required>
                                    Berhasil Diperbaiki
                                </label>
                                <label class="flex cursor-pointer items-center justify-center rounded-lg border border-slate-300 py-2 text-sm font-medium text-slate-700 has-[:checked]:border-red-600 has-[:checked]:bg-red-600 has-[:checked]:text-white">
                                    <input type="radio" name="hasil" value="rusak_berat" class="hidden" required>
                                    Rusak Berat
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Harga Jasa</label>
                            <input type="number" name="harga_jasa" min="0" step="0.01" placeholder="0"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Biaya Komponen</label>
                            <input type="number" name="biaya_komponen" min="0" step="0.01" placeholder="0"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">No. Struk</label>
                            <input type="text" name="no_struk" placeholder="cth. STR-0001"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan</label>
                            <textarea name="catatan" rows="3" placeholder="cth. ganti keyboard, sudah dites normal"
                                      class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none"></textarea>
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                            Simpan Hasil
                        </button>
                    </form>
                </div>
            @else
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6 text-center">
                    <x-icon.alert-triangle class="mx-auto mb-2 h-6 w-6 text-slate-300" />
                    <p class="text-sm text-slate-500">Laporan ini sudah selesai ditangani.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
