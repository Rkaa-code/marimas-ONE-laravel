@extends('layouts.app')

@section('title', 'Foto Aset')

@section('content')
    <x-inventaris.tab-nav active="foto-aset" />

    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
        <h2 class="text-base font-semibold text-slate-900">Foto Aset</h2>
        <p class="text-sm text-slate-500 mb-4">Kumpulan foto seluruh unit aset yang sudah tercatat.</p>

        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, merek, atau tipe aset..."
                       class="w-full pl-9 pr-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>
            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cari
            </button>
        </form>

        @if ($aset->isEmpty())
            <div class="flex flex-col items-center justify-center gap-2 py-16 text-center">
                <x-icon.image class="h-8 w-8 text-slate-300" />
                <p class="text-sm text-slate-400">Belum ada aset dengan foto tersimpan.</p>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($aset as $item)
                    <a href="{{ route('inventaris.aset.show', $item) }}"
                       class="group overflow-hidden rounded-xl border border-slate-200 hover:border-slate-300 transition">
                        <div class="aspect-square w-full overflow-hidden bg-slate-100">
                            <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->kode_aset }}"
                                 class="h-full w-full object-cover transition group-hover:scale-105">
                        </div>
                        <div class="p-3">
                            <p class="truncate text-xs font-mono font-medium text-slate-500">{{ $item->kode_aset }}</p>
                            <p class="truncate text-sm font-medium text-slate-800">
                                {{ trim(($item->merek ?? '') . ' ' . ($item->tipe ?? '')) ?: ($item->jenis?->nama ?? '-') }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($aset->hasPages())
                <div class="mt-4">
                    {{ $aset->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
