@extends('layouts.app')

@section('title', 'Audit Log')

@php
    $actionLabel = [
        'created' => 'Tambah',
        'updated' => 'Ubah',
        'deleted' => 'Hapus',
        'login' => 'Login',
        'logout' => 'Logout',
    ];
    $actionColor = [
        'created' => 'bg-emerald-50 text-emerald-700',
        'updated' => 'bg-sky-50 text-sky-700',
        'deleted' => 'bg-red-100 text-red-800',
        'login' => 'bg-slate-100 text-slate-700',
        'logout' => 'bg-slate-100 text-slate-700',
    ];
@endphp

@section('content')
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
        <h2 class="text-base font-semibold text-slate-900">Audit Log</h2>
        <p class="text-sm text-slate-500 mb-4">Riwayat aktivitas pengguna di seluruh sistem.</p>

        {{-- FILTER --}}
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari deskripsi atau tipe data..."
                       class="w-full pl-9 pr-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <select name="action" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900">
                <option value="">Semua Aksi</option>
                @foreach ($actions as $value)
                    <option value="{{ $value }}" @selected(request('action') === $value)>
                        {{ $actionLabel[$value] ?? ucfirst($value) }}
                    </option>
                @endforeach
            </select>

            <select name="user_id" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900">
                <option value="">Semua Pengguna</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((int) request('user_id') === $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="from" value="{{ request('from') }}"
                   class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900">
            <input type="date" name="to" value="{{ request('to') }}"
                   class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900">

            <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Filter
            </button>
            @if (request()->hasAny(['search', 'action', 'user_id', 'from', 'to']))
                <a href="{{ route('audit-log.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700">Reset</a>
            @endif
        </form>

        <div class="border border-slate-200 rounded-lg overflow-hidden" x-data="{ expanded: null }">
            {{-- DESKTOP TABLE --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm min-w-[900px]">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wide">
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Waktu</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Pengguna</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Aksi</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">Deskripsi</th>
                            <th class="px-6 py-3 font-medium text-left whitespace-nowrap">IP</th>
                            <th class="px-6 py-3 font-medium text-right whitespace-nowrap">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/60 transition">
                                <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-3 text-slate-700 font-medium whitespace-nowrap">{{ $log->user->name ?? 'Sistem' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span class="inline-block rounded-full font-medium whitespace-nowrap text-xs px-2.5 py-1 {{ $actionColor[$log->action] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $actionLabel[$log->action] ?? ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $log->description }}</td>
                                <td class="px-6 py-3 text-slate-400 whitespace-nowrap">{{ $log->ip_address ?? '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-right">
                                    @if ($log->old_values || $log->new_values)
                                        <button type="button" x-on:click="expanded = expanded === {{ $log->id }} ? null : {{ $log->id }}"
                                                class="text-xs font-medium text-slate-600 hover:text-slate-900 hover:underline">
                                            Lihat
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($log->old_values || $log->new_values)
                                <tr x-show="expanded === {{ $log->id }}" x-cloak class="bg-slate-50/70 border-b border-slate-50">
                                    <td colspan="6" class="px-6 py-4">
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            @if ($log->old_values)
                                                <div>
                                                    <p class="text-xs font-semibold text-slate-500 mb-1">Sebelum</p>
                                                    <pre class="text-xs bg-white border border-slate-200 rounded-lg p-3 overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                            @if ($log->new_values)
                                                <div>
                                                    <p class="text-xs font-semibold text-slate-500 mb-1">Sesudah</p>
                                                    <pre class="text-xs bg-white border border-slate-200 rounded-lg p-3 overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-sm text-slate-400 text-center py-8">Belum ada aktivitas yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div class="sm:hidden flex flex-col divide-y divide-slate-100">
                @forelse ($logs as $log)
                    <div class="px-4 py-3">
                        <button type="button" x-on:click="expanded = expanded === {{ $log->id }} ? null : {{ $log->id }}"
                                class="w-full flex items-start justify-between gap-2 text-left">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800">{{ $log->user->name ?? 'Sistem' }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $log->description }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $log->created_at->format('d M Y H:i') }}</p>
                                <span class="inline-block rounded-full font-medium whitespace-nowrap text-[10px] px-2 py-0.5 mt-1.5 {{ $actionColor[$log->action] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $actionLabel[$log->action] ?? ucfirst($log->action) }}
                                </span>
                            </div>
                        </button>

                        <div x-show="expanded === {{ $log->id }}" x-cloak class="mt-3 pt-3 border-t border-slate-100 flex flex-col gap-2">
                            @if ($log->old_values)
                                <p class="text-xs font-semibold text-slate-500">Sebelum</p>
                                <pre class="text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @endif
                            @if ($log->new_values)
                                <p class="text-xs font-semibold text-slate-500">Sesudah</p>
                                <pre class="text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada aktivitas yang tercatat.</p>
                @endforelse
            </div>

            @if ($logs->hasPages())
                <div class="px-6 py-3 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection