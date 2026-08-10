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
    $activeStatus = request('status', '');
@endphp

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Aset</h1>
            <p class="text-sm text-slate-500">Kelola aset IT per-unit — daftar, filter, dan riwayatnya.</p>
        </div>
    </div>

    {{-- CARD WRAPPER — samain kaya bungkus TabAset.tsx di React (bg-white rounded-xl p-6 shadow-sm border) --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <p class="text-sm text-slate-500">
                Kelola aset IT per-unit (laptop, monitor, dsb) beserta jenis dan statusnya.
            </p>
            <a href="{{ route('inventaris.aset.create') }}"
               class="flex items-center gap-2 bg-slate-900 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-slate-800 transition">
                + Tambah Aset
            </a>
        </div>

        {{-- TAB STATUS — versi Blade dari ScrollableTabBar, tiap tab = GET link biar kefilter server-side --}}
        <nav class="relative mb-4">
            <ul class="flex items-center gap-6 border-b border-slate-200 overflow-x-auto">
                <li class="shrink-0">
                    <a href="{{ route('inventaris.aset.index', array_filter(['search' => request('search'), 'jenis_id' => request('jenis_id')])) }}"
                       class="flex items-center gap-2 pb-3 text-sm whitespace-nowrap border-b-2 -mb-px transition-colors {{ $activeStatus === '' ? 'border-slate-900 text-slate-900 font-medium' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                        Semua Status
                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $activeStatus === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500' }}">
                            {{ $statusCounts['semua'] ?? 0 }}
                        </span>
                    </a>
                </li>
                @foreach ($statusLabel as $value => $label)
                    <li class="shrink-0">
                        <a href="{{ route('inventaris.aset.index', array_filter(['search' => request('search'), 'jenis_id' => request('jenis_id'), 'status' => $value])) }}"
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

        {{-- SEARCH + FILTER JENIS — samain style SearchInput.tsx --}}
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            @if ($activeStatus !== '')
                <input type="hidden" name="status" value="{{ $activeStatus }}">
            @endif

            <div class="relative flex-1 min-w-[220px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, merek, tipe, atau serial number..."
                       class="w-full pl-9 pr-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <select name="jenis_id" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
                <option value="">Semua Jenis</option>
                @foreach ($jenisAset as $jenis)
                    <option value="{{ $jenis->id }}" @selected(request('jenis_id') == $jenis->id)>{{ $jenis->nama }}</option>
                @endforeach
            </select>

            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Filter
            </button>

            @if (request()->hasAny(['search', 'jenis_id', 'status']))
                <a href="{{ route('inventaris.aset.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700">
                    Reset
                </a>
            @endif
        </form>

        <div class="border border-slate-200 rounded-lg overflow-hidden">
            {{-- DESKTOP: tabel 1 baris per row, overflow-x biar gak numpuk ke bawah --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm min-w-[900px]">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wide">
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Kode Aset</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Jenis</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Merek / Tipe</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Serial Number</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Supplier</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Status</th>
                            <th class="px-6 py-3 font-medium text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aset as $item)
                            <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3 font-medium text-slate-800 whitespace-nowrap">{{ $item->kode_aset }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $item->jenis?->nama ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ trim(($item->merek ?? '') . ' ' . ($item->tipe ?? '')) ?: '-' }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $item->serial_number ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $item->supplier?->nama ?? '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span class="inline-block rounded-full font-medium whitespace-nowrap text-xs px-2.5 py-1 {{ $statusColor[$item->status] }}">
                                        {{ $statusLabel[$item->status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2 flex-nowrap">
                                        <a href="{{ route('inventaris.aset.show', $item) }}" title="Detail"
                                           class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>

                                        @if ($item->status === 'tersedia' && auth()->user()?->hasRole('admin'))
                                            <x-inventaris.aset.serahkan-modal :aset="$item" compact />
                                        @endif

                                        <a href="{{ route('inventaris.aset.edit', $item) }}" title="Edit"
                                           class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 13.5V19.5a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 19.5V6.75A2.25 2.25 0 014.5 4.5h6" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('inventaris.aset.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Hapus aset ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-sm text-slate-400 text-center py-8">Belum ada aset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE: list card expandable, samain pola card di TabAset.tsx --}}
            <div class="sm:hidden flex flex-col divide-y divide-slate-100" x-data="{ expanded: null }">
                @forelse ($aset as $item)
                    <div class="px-4 py-3">
                        <button type="button" x-on:click="expanded = expanded === {{ $item->id }} ? null : {{ $item->id }}"
                                class="w-full flex items-start justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $item->kode_aset }}</p>
                                <p class="text-xs text-slate-500 truncate">
                                    {{ $item->jenis?->nama ?? '-' }} &middot; {{ trim(($item->merek ?? '') . ' ' . ($item->tipe ?? '')) ?: '-' }}
                                </p>
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
                            <p class="text-xs text-slate-500">Serial Number: <span class="text-slate-700 font-medium">{{ $item->serial_number ?? '-' }}</span></p>
                            <p class="text-xs text-slate-500">Supplier: <span class="text-slate-700 font-medium">{{ $item->supplier?->nama ?? '-' }}</span></p>
                            <div class="flex items-center flex-wrap gap-3 mt-1">
                                <a href="{{ route('inventaris.aset.show', $item) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Detail</a>
                                <a href="{{ route('inventaris.aset.edit', $item) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Edit</a>
                                <form action="{{ route('inventaris.aset.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Hapus aset ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada aset.</p>
                @endforelse
            </div>

            @if ($aset->hasPages())
                <div class="px-6 py-3 border-t border-slate-100">
                    {{ $aset->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection