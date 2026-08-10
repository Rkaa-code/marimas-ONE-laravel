@extends('layouts.app')

@section('title', 'Tambah Cabang')

@section('content')
    <div class="max-w-lg mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-900">Tambah Cabang</h1>
            <p class="text-sm text-slate-500">Isi data cabang baru di bawah ini.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="POST" action="{{ route('cabang.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Nama Cabang</label>
                    <input
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('nama') border-red-400 @enderror"
                    >
                    @error('nama')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-700">Alamat</label>
                    <textarea
                        name="alamat"
                        rows="3"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('alamat') border-red-400 @enderror"
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-700">Telepon</label>
                        <input
                            type="text"
                            name="telepon"
                            value="{{ old('telepon') }}"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('telepon') border-red-400 @enderror"
                        >
                        @error('telepon')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-700">Link Lokasi</label>
                        <input
                            type="text"
                            name="link"
                            value="{{ old('link') }}"
                            placeholder="https://..."
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('link') border-red-400 @enderror"
                        >
                        @error('link')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a
                        href="{{ route('cabang.index') }}"
                        class="rounded-lg border border-slate-200 px-4 py-2 text-sm hover:bg-slate-50"
                    >Batal</a>
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-800"
                    >Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection