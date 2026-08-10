@props(['aset'])

<div x-data="laporRusakForm({{ $aset->id }})" x-cloak>
    <button type="button" x-on:click="open = true" title="Lapor Rusak"
            class="flex h-8 items-center gap-1.5 rounded-full bg-red-600 px-3 text-xs font-medium text-white hover:bg-red-700">
        <x-icon.wrench class="h-3.5 w-3.5" />
        Lapor Rusak
    </button>

    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/40" x-on:click="open = false"></div>

        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-start justify-between">
                <h2 class="text-base font-semibold text-slate-900">Lapor Rusak {{ $aset->kode_aset }}</h2>
                <button type="button" x-on:click="open = false" class="text-slate-400 hover:text-slate-600">
                    <x-icon.x class="h-5 w-5" />
                </button>
            </div>

            <form x-on:submit.prevent="submit">
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Kerusakan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" x-on:click="jenisKerusakan = 'hardware'"
                                :class="jenisKerusakan === 'hardware' ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700'"
                                class="rounded-lg py-2 text-sm font-medium">Hardware</button>
                        <button type="button" x-on:click="jenisKerusakan = 'software'"
                                :class="jenisKerusakan === 'software' ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700'"
                                class="rounded-lg py-2 text-sm font-medium">Software</button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Keluhan <span class="text-red-500">*</span>
                    </label>
                    <textarea x-model="keluhan" rows="3" placeholder="cth. keyboard tidak berfungsi sebelah kiri"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none"></textarea>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Foto Bukti Kerusakan (<span x-text="fotoKerusakan.length"></span> Foto)
                        <span class="text-red-500">*</span>
                        <span class="font-normal text-slate-400">(maks. 3 foto, 3MB/foto)</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(foto, idx) in fotoKerusakanPreviews" :key="idx">
                            <div class="relative h-20 w-20 overflow-hidden rounded-lg border border-slate-200">
                                <img :src="foto" class="h-full w-full object-cover">
                                <button type="button" x-on:click="hapusFotoKerusakan(idx)"
                                        class="absolute right-0.5 top-0.5 rounded-full bg-black/60 p-0.5 text-white">
                                    <x-icon.x class="h-3 w-3" />
                                </button>
                            </div>
                        </template>
                        <label x-show="fotoKerusakan.length < 3"
                               class="flex h-20 w-20 cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border border-dashed border-slate-300 text-slate-400 hover:border-slate-400">
                            <input type="file" accept="image/*" class="hidden" x-on:change="tambahFotoKerusakan">
                            <span class="text-lg">+</span>
                            <span class="text-[10px]">Tambah</span>
                        </label>
                    </div>
                </div>

                <p x-show="error" x-text="error" class="mb-3 text-sm text-red-600"></p>

                <button type="submit" :disabled="submitting"
                        class="w-full rounded-lg bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50">
                    <span x-show="!submitting">Lapor Rusak</span>
                    <span x-show="submitting">Mengirim...</span>
                </button>
            </form>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        function laporRusakForm(asetId) {
            return {
                open: false,
                jenisKerusakan: 'hardware',
                keluhan: '',
                fotoKerusakan: [],
                fotoKerusakanPreviews: [],
                submitting: false,
                error: null,

                tambahFotoKerusakan(e) {
                    const file = e.target.files[0];
                    if (!file || this.fotoKerusakan.length >= 3) return;
                    if (file.size > 3 * 1024 * 1024) {
                        this.error = 'Ukuran foto kerusakan maksimal 3MB.';
                        e.target.value = '';
                        return;
                    }
                    this.fotoKerusakan.push(file);
                    this.fotoKerusakanPreviews.push(URL.createObjectURL(file));
                    e.target.value = '';
                },

                hapusFotoKerusakan(idx) {
                    this.fotoKerusakan.splice(idx, 1);
                    this.fotoKerusakanPreviews.splice(idx, 1);
                },

                async submit() {
                    this.error = null;

                    if (!this.keluhan.trim()) { this.error = 'Isi keluhan kerusakannya dulu.'; return; }
                    if (this.fotoKerusakan.length < 1) { this.error = 'Minimal 1 foto bukti kerusakan.'; return; }

                    const fd = new FormData();
                    fd.append('jenis_kerusakan', this.jenisKerusakan);
                    fd.append('keluhan', this.keluhan);
                    this.fotoKerusakan.forEach(f => fd.append('foto_kerusakan[]', f));

                    this.submitting = true;
                    const res = await fetch(`/inventaris/aset/${asetId}/lapor-rusak`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: fd,
                    });

                    if (res.redirected) {
                        window.location.href = res.url;
                        return;
                    }
                    if (!res.ok) {
                        const data = await res.json().catch(() => null);
                        this.error = data?.message || 'Gagal mengirim laporan kerusakan.';
                        this.submitting = false;
                        return;
                    }
                    window.location.reload();
                },
            };
        }
    </script>
    @endpush
@endonce
