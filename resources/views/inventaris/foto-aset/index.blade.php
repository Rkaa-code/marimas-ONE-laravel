@extends('layouts.app')

@section('title', 'Foto Aset')

@php
    $subTabs = [
        'peminjaman' => ['label' => 'Peminjaman', 'count' => $counts['peminjaman']],
        'pengembalian' => ['label' => 'Pengembalian', 'count' => $counts['pengembalian']],
        'rusak' => ['label' => 'Rusak', 'count' => $counts['rusak']],
        'semua' => ['label' => 'Semua', 'count' => null],
    ];
    $statusLabel = [
        'peminjaman' => 'Peminjaman',
        'pengembalian' => 'Pengembalian',
        'rusak' => 'Rusak',
    ];
    $statusColor = [
        'peminjaman' => 'bg-sky-50 text-sky-700',
        'pengembalian' => 'bg-emerald-50 text-emerald-700',
        'rusak' => 'bg-red-100 text-red-800',
    ];
    $tanggalColLabel = match ($tab) {
        'pengembalian' => 'Tgl Pengembalian',
        'rusak' => 'Tgl Lapor',
        'semua' => 'Tanggal',
        default => 'Tgl Serah Terima',
    };
    $namaColLabel = $tab === 'rusak' ? 'Pelapor' : 'Pemakai';
@endphp

@section('content')
    <x-inventaris.tab-nav active="foto-aset" />

    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
        <h2 class="text-base font-semibold text-slate-900">Foto Aset</h2>
        <p class="text-sm text-slate-500 mb-4">Bukti foto peminjaman, pengembalian, dan laporan kerusakan aset.</p>

        {{-- SUB-TAB --}}
        <nav class="relative mb-4">
            <ul class="flex items-center gap-6 border-b border-slate-200 overflow-x-auto">
                @foreach ($subTabs as $value => $info)
                    <li class="shrink-0">
                        <a href="{{ route('inventaris.foto-aset.index', array_filter(['search' => request('search'), 'tab' => $value])) }}"
                           class="flex items-center gap-2 pb-3 text-sm whitespace-nowrap border-b-2 -mb-px transition-colors {{ $tab === $value ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                            {{ $info['label'] }}
                            @if (!is_null($info['count']))
                                <span class="text-xs px-1.5 py-0.5 rounded-full {{ $tab === $value ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $info['count'] }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- SEARCH --}}
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="relative flex-1 min-w-[220px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode aset, merek, atau tipe..."
                       class="w-full pl-9 pr-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>
            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cari
            </button>
            @if (request()->hasAny(['search']))
                <a href="{{ route('inventaris.foto-aset.index', ['tab' => $tab]) }}"
                   class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        <div class="border border-slate-200 rounded-lg overflow-hidden">
            {{-- DESKTOP TABLE --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm min-w-[900px]">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wide">
                            @if ($tab === 'semua')
                                <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Status</th>
                            @endif
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Aset</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">{{ $namaColLabel }}</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">{{ $tanggalColLabel }}</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Jumlah Foto</th>
                            <th class="px-6 py-3 font-medium text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            @php
                                $row = $tab === 'semua' ? $item : [
                                    'status' => $tab,
                                    'aset' => $item->aset,
                                    'nama' => $tab === 'rusak' ? $item->pelapor?->name : $item->penerima?->name,
                                    'tanggal' => match ($tab) {
                                        'pengembalian' => $item->tanggal_kembali,
                                        'rusak' => $item->tanggal_lapor,
                                        default => $item->tanggal_serah,
                                    },
                                    'jumlah_foto' => count(match ($tab) {
                                        'pengembalian' => $item->foto_kembali ?? [],
                                        'rusak' => $item->foto_kerusakan ?? [],
                                        default => $item->foto_serah ?? [],
                                    }),
                                    'fotos' => collect(match ($tab) {
                                        'pengembalian' => $item->foto_kembali ?? [],
                                        'rusak' => $item->foto_kerusakan ?? [],
                                        default => $item->foto_serah ?? [],
                                    })->map(fn ($p) => \Illuminate\Support\Facades\Storage::url($p))->all(),
                                ];
                            @endphp
                            <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/60 transition">
                                @if ($tab === 'semua')
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <span class="inline-block rounded-full font-medium whitespace-nowrap text-xs px-2.5 py-1 {{ $statusColor[$row['status']] }}">
                                            {{ $statusLabel[$row['status']] }}
                                        </span>
                                    </td>
                                @endif
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <p class="font-semibold text-slate-800">{{ $row['aset']->kode_aset ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ trim(($row['aset']->merek ?? '') . ' ' . ($row['aset']->tipe ?? '')) ?: '-' }}</p>
                                </td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $row['nama'] ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">
                                    {{ $row['tanggal']?->format('d M Y, H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $row['jumlah_foto'] }} foto</td>
                                <td class="px-6 py-3 whitespace-nowrap text-right">
                                    <x-inventaris.foto-aset.lihat-foto-modal
                                        :title="$row['aset']->kode_aset ?? 'Foto Aset'"
                                        :subtitle="($statusLabel[$row['status']] ?? '') . ' &middot; ' . ($row['nama'] ?? '-')"
                                        :fotos="$row['fotos']" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $tab === 'semua' ? 6 : 5 }}" class="text-sm text-slate-400 text-center py-8">
                                    Belum ada foto di kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div class="sm:hidden flex flex-col divide-y divide-slate-100">
                @forelse ($data as $item)
                    @php
                        $row = $tab === 'semua' ? $item : [
                            'status' => $tab,
                            'aset' => $item->aset,
                            'nama' => $tab === 'rusak' ? $item->pelapor?->name : $item->penerima?->name,
                            'tanggal' => match ($tab) {
                                'pengembalian' => $item->tanggal_kembali,
                                'rusak' => $item->tanggal_lapor,
                                default => $item->tanggal_serah,
                            },
                            'jumlah_foto' => count(match ($tab) {
                                'pengembalian' => $item->foto_kembali ?? [],
                                'rusak' => $item->foto_kerusakan ?? [],
                                default => $item->foto_serah ?? [],
                            }),
                            'fotos' => collect(match ($tab) {
                                'pengembalian' => $item->foto_kembali ?? [],
                                'rusak' => $item->foto_kerusakan ?? [],
                                default => $item->foto_serah ?? [],
                            })->map(fn ($p) => \Illuminate\Support\Facades\Storage::url($p))->all(),
                        ];
                    @endphp
                    <div class="px-4 py-3 flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            @if ($tab === 'semua')
                                <span class="inline-block rounded-full font-medium whitespace-nowrap text-[10px] px-2 py-0.5 mb-1 {{ $statusColor[$row['status']] }}">
                                    {{ $statusLabel[$row['status']] }}
                                </span>
                            @endif
                            <p class="text-sm font-semibold text-slate-800">{{ $row['aset']->kode_aset ?? '-' }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $row['nama'] ?? '-' }} &middot; {{ $row['tanggal']?->format('d M Y, H:i') ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $row['jumlah_foto'] }} foto</p>
                        </div>
                        <x-inventaris.foto-aset.lihat-foto-modal
                            :title="$row['aset']->kode_aset ?? 'Foto Aset'"
                            :subtitle="($statusLabel[$row['status']] ?? '') . ' &middot; ' . ($row['nama'] ?? '-')"
                            :fotos="$row['fotos']" />
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada foto di kategori ini.</p>
                @endforelse
            </div>

            @if ($data->hasPages())
                <div class="px-6 py-3 border-t border-slate-100">
                    {{ $data->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
