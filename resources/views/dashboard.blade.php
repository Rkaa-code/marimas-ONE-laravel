@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900">Selamat datang{{ session('dummy_user_name') ? ', ' . session('dummy_user_name') : '' }} 👋</h1>
        <p class="text-sm text-slate-500">Ringkasan singkat kondisi inventaris dan karyawan saat ini. (Data dummy)</p>
    </div>

    {{-- Kartu KPI --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Aset Aktif</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">248</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-900 text-white">
                    <x-icon.package class="h-5 w-5" />
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-400">Update terakhir hari ini (Sumber: Menu Aset)</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Karyawan Aktif</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">57</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-600 text-white">
                    <x-icon.users class="h-5 w-5" />
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-400">Update terakhir hari ini (Sumber: Menu Karyawan)</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Cabang Terdaftar</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">6</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500 text-white">
                    <x-icon.building class="h-5 w-5" />
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-400">Update terakhir hari ini (Sumber: Menu Cabang)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Aktivitas terbaru --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Aktivitas Terbaru</h2>
                <x-icon.trending-up class="h-4 w-4 text-slate-400" />
            </div>
            <ul class="divide-y divide-slate-100 text-sm">
                <li class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-slate-800">Laptop Dell Latitude dipinjam oleh Budi Santoso</p>
                        <p class="text-xs text-slate-400">Cabang Jakarta Pusat</p>
                    </div>
                    <span class="text-xs text-slate-400">10 menit lalu</span>
                </li>
                <li class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-slate-800">Laporan kerusakan printer HP LaserJet</p>
                        <p class="text-xs text-slate-400">Cabang Surabaya</p>
                    </div>
                    <span class="text-xs text-slate-400">1 jam lalu</span>
                </li>
                <li class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-slate-800">Aset baru ditambahkan: Monitor LG 24"</p>
                        <p class="text-xs text-slate-400">Cabang Bandung</p>
                    </div>
                    <span class="text-xs text-slate-400">3 jam lalu</span>
                </li>
                <li class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-slate-800">Absen wajah tercatat untuk 42 karyawan</p>
                        <p class="text-xs text-slate-400">Semua cabang</p>
                    </div>
                    <span class="text-xs text-slate-400">Hari ini, 08:02</span>
                </li>
            </ul>
        </div>

        {{-- Status aset ringkas --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold text-slate-900">Status Aset</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Tersedia</span>
                    <span class="font-medium text-emerald-700">132</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Dipakai</span>
                    <span class="font-medium text-amber-700">94</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Menunggu Perbaikan</span>
                    <span class="font-medium text-yellow-700">12</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Sedang Diperbaiki</span>
                    <span class="font-medium text-sky-700">7</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-600">Rusak Berat</span>
                    <span class="font-medium text-red-700">3</span>
                </div>
            </div>
        </div>
    </div>
@endsection