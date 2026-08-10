@props(['pemakai'])

<div x-data="kembalikanAsetForm({{ $pemakai->id }})" x-cloak>
    <button type="button" x-on:click="open = true"
            class="rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-medium text-amber-800 hover:bg-amber-100">
        Tandai Dikembalikan
    </button>

    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/40" x-on:click="open = false"></div>

        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-start justify-between">
                <h2 class="text-base font-semibold text-slate-900">Kembalikan Aset {{ $pemakai->aset->kode_aset }}</h2>
                <button type="button" x-on:click="open = false" class="text-slate-400 hover:text-slate-600">
                    <x-icon.x class="h-5 w-5" />
                </button>
            </div>

            <div class="mb-4 rounded-lg bg-slate-50 px-3 py-2.5 text-sm text-slate-500">
                Kamu sedang memakai
                <p class="font-semibold text-slate-900">{{ $pemakai->penerima->name }}</p>
            </div>

            <form x-on:submit.prevent="submit">
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Kode Struk Penerimaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="kodeStruk" placeholder="cth. STJ-20260722-0001"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm font-mono focus:border-slate-500 focus:outline-none">
                    <p class="mt-1 text-xs text-slate-400">
                        Cek struk penerimaan fisik yang kamu terima waktu serah-terima aset ini, lalu ketik kodenya di sini.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Pengembalian</label>
                    <input type="date" x-model="tanggalKembali"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan</label>
                    <textarea x-model="catatan" rows="2" placeholder="cth. dikembalikan dalam kondisi baik"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none"></textarea>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Foto Bukti Kondisi Aset (<span x-text="fotos.length"></span> Foto)
                        <span class="text-red-500">*</span>
                        <span class="font-normal text-slate-400">(maks. 3 foto)</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(foto, idx) in fotoPreviews" :key="idx">
                            <div class="relative h-20 w-20 overflow-hidden rounded-lg border border-slate-200">
                                <img :src="foto" class="h-full w-full object-cover">
                                <button type="button" x-on:click="hapusFoto(idx)"
                                        class="absolute right-0.5 top-0.5 rounded-full bg-black/60 p-0.5 text-white">
                                    <x-icon.x class="h-3 w-3" />
                                </button>
                            </div>
                        </template>
                        <label x-show="fotos.length < 3"
                               class="flex h-20 w-20 cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border border-dashed border-slate-300 text-slate-400 hover:border-slate-400">
                            <input type="file" accept="image/*" class="hidden" x-on:change="tambahFoto">
                            <span class="text-lg">+</span>
                            <span class="text-[10px]">Tambah</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4 rounded-lg border border-slate-200 p-3">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="checkbox" x-model="isRusak" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm font-medium text-slate-700">Aset dikembalikan dalam kondisi rusak</span>
                    </label>

                    <div x-show="isRusak" x-cloak class="mt-3 space-y-3 border-t border-slate-100 pt-3">
                        <div>
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
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Keluhan</label>
                            <textarea x-model="keluhan" rows="2" placeholder="cth. keyboard tidak berfungsi sebelah kiri"
                                      class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <p x-show="error" x-text="error" class="mb-3 text-sm text-red-600"></p>

                <button type="submit" :disabled="submitting"
                        :class="isRusak ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                        class="w-full rounded-lg py-2.5 text-sm font-semibold text-white disabled:opacity-50">
                    <span x-show="!submitting" x-text="isRusak ? 'Kembalikan & Lapor Rusak' : 'Kembalikan'"></span>
                    <span x-show="submitting">Menyimpan...</span>
                </button>
            </form>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        function kembalikanAsetForm(pemakaiId) {
            return {
                open: false,
                kodeStruk: '',
                tanggalKembali: new Date().toISOString().slice(0, 10),
                catatan: '',
                fotos: [],
                fotoPreviews: [],
                isRusak: false,
                jenisKerusakan: 'hardware',
                keluhan: '',
                submitting: false,
                error: null,

                tambahFoto(e) {
                    const file = e.target.files[0];
                    if (!file || this.fotos.length >= 3) return;
                    this.fotos.push(file);
                    this.fotoPreviews.push(URL.createObjectURL(file));
                    e.target.value = '';
                },

                hapusFoto(idx) {
                    this.fotos.splice(idx, 1);
                    this.fotoPreviews.splice(idx, 1);
                },

                async submit() {
                    this.error = null;

                    if (!this.kodeStruk.trim()) { this.error = 'Isi kode struk penerimaan dulu.'; return; }
                    if (this.fotos.length < 1) { this.error = 'Minimal 1 foto bukti kondisi aset.'; return; }
                    if (this.isRusak && !this.keluhan.trim()) { this.error = 'Isi keluhan kerusakannya dulu.'; return; }

                    const fd = new FormData();
                    fd.append('kode_struk', this.kodeStruk);
                    fd.append('tanggal_kembali', this.tanggalKembali);
                    fd.append('catatan_kembali', this.catatan);
                    fd.append('is_rusak', this.isRusak ? '1' : '0');
                    if (this.isRusak) {
                        fd.append('jenis_kerusakan', this.jenisKerusakan);
                        fd.append('keluhan', this.keluhan);
                    }
                    this.fotos.forEach(f => fd.append('foto_kembali[]', f));

                    this.submitting = true;
                    const res = await fetch(`/inventaris/aset-pemakai/${pemakaiId}/kembalikan`, {
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
                        this.error = data?.message || 'Gagal menandai pengembalian.';
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