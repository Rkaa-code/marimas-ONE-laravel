@extends('layouts.app')

@section('title', 'Notifikasi')

@php
    $levelColor = [
        'success' => 'bg-emerald-50 text-emerald-700',
        'warning' => 'bg-amber-50 text-amber-700',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-sky-50 text-sky-700',
    ];
    $levelDot = [
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-red-500',
        'info' => 'bg-sky-500',
    ];
@endphp

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Notifikasi</h2>
                <p class="text-sm text-slate-500">Pembaruan info aset: aset baru, edit data, ganti status, sampai dihapus.</p>
            </div>

            @if ($notifikasi->contains(fn ($n) => $n->read_at === null))
                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($notifikasi as $item)
                <form method="POST" action="{{ route('notifikasi.baca', $item->id) }}" class="block">
                    @csrf
                    <button type="submit" class="flex w-full items-start gap-3 px-1 py-3.5 text-left transition hover:bg-slate-50 {{ $item->read_at === null ? 'bg-slate-50/70' : '' }}">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $levelDot[$item->data['level'] ?? 'info'] ?? 'bg-sky-500' }}"></span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-slate-800">{{ $item->data['title'] ?? '' }}</span>
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-medium {{ $levelColor[$item->data['level'] ?? 'info'] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $item->data['kode_aset'] ?? '' }}
                                </span>
                            </span>
                            <span class="mt-0.5 block text-sm text-slate-500">{{ $item->data['message'] ?? '' }}</span>
                            <span class="mt-1 block text-xs text-slate-400">{{ $item->created_at->diffForHumans() }}</span>
                        </span>
                        @if ($item->read_at === null)
                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-500"></span>
                        @endif
                    </button>
                </form>
            @empty
                <p class="py-10 text-center text-sm text-slate-400">Belum ada notifikasi.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $notifikasi->links() }}
        </div>
    </div>
@endsection
