@php
    $aset = $aset ?? null;
    $statusLabel = [
        'tersedia' => 'Tersedia',
        'dipakai' => 'Dipakai',
        'menunggu_perbaikan' => 'Menunggu Perbaikan',
        'sedang_diperbaiki' => 'Sedang Diperbaiki',
        'rusak_berat' => 'Rusak Berat',
        'dijual' => 'Dijual',
    ];
    $selectedKelengkapan = $aset ? $aset->kelengkapan->pluck('id')->toArray() : [];
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Jenis Aset <span class="text-red-500">*</span></label>
        <select name="jenis_id" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            <option value="">Pilih jenis aset</option>
            @foreach ($jenisAset as $jenis)
                <option value="{{ $jenis->id }}" @selected(old('jenis_id', $aset?->jenis_id) == $jenis->id)>{{ $jenis->nama }}</option>
            @endforeach
        </select>
        @error('jenis_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Supplier</label>
        <select name="supplier_id"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            <option value="">Pilih supplier</option>
            @foreach ($supplier as $item)
                <option value="{{ $item->id }}" @selected(old('supplier_id', $aset?->supplier_id) == $item->id)>{{ $item->nama }}</option>
            @endforeach
        </select>
        @error('supplier_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Merek</label>
        <input type="text" name="merek" value="{{ old('merek', $aset?->merek) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Tipe</label>
        <input type="text" name="tipe" value="{{ old('tipe', $aset?->tipe) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Warna</label>
        <input type="text" name="warna" value="{{ old('warna', $aset?->warna) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Serial Number</label>
        <input type="text" name="serial_number" value="{{ old('serial_number', $aset?->serial_number) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Pembelian</label>
        <input type="date" name="tanggal_pembelian"
               value="{{ old('tanggal_pembelian', $aset?->tanggal_pembelian?->format('Y-m-d')) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Garansi</label>
        <input type="date" name="tanggal_garansi"
               value="{{ old('tanggal_garansi', $aset?->tanggal_garansi?->format('Y-m-d')) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">No Surat Jalan</label>
        <input type="text" name="no_surat_jalan" value="{{ old('no_surat_jalan', $aset?->no_surat_jalan) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">No Good Receive</label>
        <input type="text" name="no_good_receive" value="{{ old('no_good_receive', $aset?->no_good_receive) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Perusahaan</label>
        <input type="text" name="perusahaan" value="{{ old('perusahaan', $aset?->perusahaan) }}"
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
        <select name="status" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            @foreach ($statusLabel as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $aset?->status ?? 'tersedia') == $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6">
    <label class="mb-1 block text-sm font-medium text-slate-700">Keterangan</label>
    <textarea name="keterangan" rows="3"
              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">{{ old('keterangan', $aset?->keterangan) }}</textarea>
</div>

<div class="mt-6">
    <label class="mb-1 block text-sm font-medium text-slate-700">Foto</label>
    @if ($aset?->foto)
        <img src="{{ Storage::url($aset->foto) }}" alt="Foto aset" class="mb-2 h-24 w-24 rounded-lg object-cover">
    @endif
    <input type="file" name="foto" accept="image/*"
           class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800">
    @error('foto') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-6">
    <label class="mb-2 block text-sm font-medium text-slate-700">Kelengkapan</label>
    <div class="grid grid-cols-2 gap-2 md:grid-cols-3">
        @forelse ($kelengkapanMaster as $item)
            <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <input type="checkbox" name="kelengkapan[]" value="{{ $item->id }}"
                       @checked(in_array($item->id, old('kelengkapan', $selectedKelengkapan)))
                       class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                {{ $item->nama }}
            </label>
        @empty
            <p class="text-sm text-slate-400">Belum ada data kelengkapan. Tambah dulu di menu Master Data.</p>
        @endforelse
    </div>
</div>
