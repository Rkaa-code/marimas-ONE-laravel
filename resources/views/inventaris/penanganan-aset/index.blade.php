@extends('layouts.app')

@section('title', 'Penanganan Aset')

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
    $tanggalColLabel = match ($activeStatus) {
        'menunggu_terima' => 'Tanggal Lapor',
        'sedang_diperbaiki' => 'Tanggal Terima',
        default => 'Tanggal Selesai',
    };
@endphp

@section('content')
    <x-inventaris.tab-nav active="penanganan-aset" />

    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
        <h2 class="text-base font-semibold text-slate-900">Forum Penanganan Aset</h2>
        <p class="text-sm text-slate-500 mb-4">Laporan kerusakan dari peminjam yang belum/sudah ditangani.</p>

        {{-- SUB-TAB STATUS --}}
        <nav class="relative mb-4">
            <ul class="flex items-center gap-6 border-b border-slate-200 overflow-x-auto">
                @foreach ($statusLabel as $value => $label)
                    <li class="shrink-0">
                        <a href="{{ route('inventaris.penanganan-aset.index', array_filter(['search' => request('search'), 'status' => $value])) }}"
                           class="flex items-center gap-2 pb-3 text-sm whitespace-nowrap border-b-2 -mb-px transition-colors {{ $activeStatus === $value ? 'border-slate-900 text-slate-900 font-medium' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                            {{ $label }}
                            <span class="text-xs px-1.5 py-0.5 rounded-full {{ $activeStatus === $value ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $statusCounts[$value] ?? 0 }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- SEARCH --}}
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <input type="hidden" name="status" value="{{ $activeStatus }}">
            <div class="relative flex-1 min-w-[220px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari aset {{ strtolower($statusLabel[$activeStatus]) }} (kode aset, keluhan, pelapor)..."
                       class="w-full pl-9 pr-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>
            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cari
            </button>
            @if (request()->hasAny(['search']))
                <a href="{{ route('inventaris.penanganan-aset.index', ['status' => $activeStatus]) }}"
                   class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        <div class="border border-slate-200 rounded-lg overflow-hidden">
            {{-- DESKTOP TABLE --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm min-w-[900px]">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wide">
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Kode Aset</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Kerusakan</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Pelapor</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">{{ $tanggalColLabel }}</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Status</th>
                            <th class="px-6 py-3 font-medium text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penanganan as $item)
                            @php
                                $tanggal = match ($activeStatus) {
                                    'menunggu_terima' => $item->tanggal_lapor,
                                    'sedang_diperbaiki' => $item->tanggal_terima,
                                    default => $item->tanggal_selesai,
                                };
                            @endphp
                            <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3 font-semibold text-slate-800 whitespace-nowrap">{{ $item->aset->kode_aset ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap capitalize">{{ $item->jenis_kerusakan }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $item->pelapor->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $tanggal?->format('d M Y') ?? '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span class="inline-block rounded-full font-medium whitespace-nowrap text-xs px-2.5 py-1 {{ $statusColor[$item->status] }}">
                                        {{ $statusLabel[$item->status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2 flex-nowrap">
                                        @if ($item->status === 'menunggu_terima')
                                            <form action="{{ route('inventaris.penanganan-aset.terima', $item) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800">
                                                    Terima
                                                </button>
                                            </form>
                                        @elseif ($item->status === 'sedang_diperbaiki')
                                            <a href="{{ route('inventaris.penanganan-aset.show', $item) }}"
                                               class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800">
                                                Selesaikan
                                            </a>
                                        @endif

                                        <a href="{{ route('inventaris.penanganan-aset.show', $item) }}" title="Detail"
                                           class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                                            <x-icon.eye class="h-4 w-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-sm text-slate-400 text-center py-8">Belum ada laporan di status ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div class="sm:hidden flex flex-col divide-y divide-slate-100" x-data="{ expanded: null }">
                @forelse ($penanganan as $item)
                    <div class="px-4 py-3">
                        <button type="button" x-on:click="expanded = expanded === {{ $item->id }} ? null : {{ $item->id }}"
                                class="w-full flex items-start justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800">{{ $item->aset->kode_aset ?? '-' }}</p>
                                <p class="text-xs text-slate-500 truncate capitalize">{{ $item->jenis_kerusakan }} &middot; {{ $item->pelapor->name ?? '-' }}</p>
                                <span class="inline-block rounded-full font-medium whitespace-nowrap text-[10px] px-2 py-0.5 mt-1.5 {{ $statusColor[$item->status] }}">
                                    {{ $statusLabel[$item->status] }}
                                </span>
                            </div>
                            <svg class="text-slate-400 flex-shrink-0 mt-1 w-4 h-4 transition-transform"
                                 x-bind:class="expanded === {{ $item->id }} ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="expanded === {{ $item->id }}" x-cloak class="mt-3 pt-3 border-t border-slate-100 flex flex-col gap-2">
                            <p class="text-xs text-slate-500">Keluhan: <span class="text-slate-700">{{ $item->keluhan }}</span></p>
                            <div class="flex items-center flex-wrap gap-3 mt-1">
                                @if ($item->status === 'menunggu_terima')
                                    <form action="{{ route('inventaris.penanganan-aset.terima', $item) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium text-slate-900 hover:underline">Terima</button>
                                    </form>
                                @endif
                                <a href="{{ route('inventaris.penanganan-aset.show', $item) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Detail</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada laporan di status ini.</p>
                @endforelse
            </div>

            @if ($penanganan->hasPages())
                <div class="px-6 py-3 border-t border-slate-100">
                    {{ $penanganan->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
