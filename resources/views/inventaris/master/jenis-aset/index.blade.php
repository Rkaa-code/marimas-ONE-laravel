@extends('layouts.app')

@section('title', 'Jenis Aset')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Jenis Aset</h1>
            <p class="text-sm text-slate-500">Kategori aset yang dipakai saat mendata aset.</p>
        </div>
        <button type="button" x-data x-on:click="window.dispatchEvent(new CustomEvent('open-modal-jenis-aset-create'))"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            + Tambah Jenis Aset
        </button>
    </div>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama jenis aset..."
               class="w-full max-w-sm rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Jumlah Aset</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($jenisAset as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->nama }}</td>
                        <td class="px-4 py-3">{{ $item->aset_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" x-data
                                    x-on:click="window.dispatchEvent(new CustomEvent('open-modal-jenis-aset-edit-{{ $item->id }}'))"
                                    class="text-sm font-medium text-slate-600 hover:text-slate-900">Edit</button>

                            <form action="{{ route('inventaris.master.jenis-aset.destroy', $item) }}" method="POST"
                                  class="inline" onsubmit="return confirm('Hapus jenis aset ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 text-sm font-medium text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    <x-modal name="jenis-aset-edit-{{ $item->id }}" title="Edit Jenis Aset">
                        <form action="{{ route('inventaris.master.jenis-aset.update', $item) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Nama</label>
                                <input type="text" name="nama" value="{{ $item->nama }}" required
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" x-on:click="open = false"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Batal</button>
                                <button type="submit"
                                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Simpan</button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400">Belum ada jenis aset.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $jenisAset->links() }}
    </div>

    <x-modal name="jenis-aset-create" title="Tambah Jenis Aset">
        <form action="{{ route('inventaris.master.jenis-aset.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Nama</label>
                <input type="text" name="nama" required placeholder="mis. Laptop"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                <p class="mt-1 text-xs text-slate-400">Kode aset (mis. IT-2026-LAPTOP-001) otomatis dibikin dari nama ini.</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" x-on:click="open = false"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Simpan</button>
            </div>
        </form>
    </x-modal>
@endsection