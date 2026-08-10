@extends('layouts.app')

@section('title', 'Tambah Aset')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900">Tambah Aset</h1>
        <p class="text-sm text-slate-500">Isi data aset baru.</p>
    </div>

    <form action="{{ route('inventaris.aset.store') }}" method="POST" enctype="multipart/form-data"
          class="rounded-xl border border-slate-200 bg-white p-6">
        @csrf

        @include('inventaris.aset._form')

        <div class="mt-8 flex justify-end gap-2">
            <a href="{{ route('inventaris.aset.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Batal</a>
            <button type="submit"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Simpan Aset</button>
        </div>
    </form>
@endsection
