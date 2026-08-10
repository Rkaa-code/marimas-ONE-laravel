@props(['aset'])

<div x-data="serahkanAsetForm({{ $aset->id }})" x-cloak>
    <button type="button" x-on:click="open = true"
            class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
        Serahkan Aset
    </button>

    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/40" x-on:click="open = false"></div>

        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-start justify-between">
                <h2 class="text-base font-semibold text-slate-900">Serahkan Aset {{ $aset->kode_aset }}</h2>
                <button type="button" x-on:click="open = false" class="text-slate-400 hover:text-slate-600">
                    <x-icon.x class="h-5 w-5" />
                </button>
            </div>

            <form x-on:submit.prevent="submit">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Serahkan Kepada</label>
                <div class="mb-4 grid grid-cols-2 gap-2">
                    <button type="button" x-on:click="setTujuan('karyawan')"
                            :class="tujuan === 'karyawan' ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700'"
                            class="rounded-lg py-2 text-sm font-medium">Karyawan</button>
                    <button type="button" x-on:click="setTujuan('cabang')"
                            :class="tujuan === 'cabang' ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700'"
                            class="rounded-lg py-2 text-sm font-medium">Cabang</button>
                </div>

                <div class="relative mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700"
                           x-text="tujuan === 'cabang' ? 'Cabang Penerima' : 'Karyawan Penerima'"></label>
                    <div class="relative">
                        <x-icon.user class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="text" x-model="query" x-on:input.debounce.300ms="cariPenerima"
                               x-on:focus="showResults = true"
                               :placeholder="tujuan === 'cabang' ? 'Cari nama cabang...' : 'Cari nama karyawan...'"
                               class="w-full rounded-lg border border-slate-300 py-2.5 pl-9 pr-3 text-sm focus:border-slate-500 focus:outline-none">
                    </div>
                    <div x-show="showResults && results.length > 0" x-on:click.outside="showResults = false"
                         class="absolute z-10 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg">
                        <template x-for="user in results" :key="user.id">
                            <button type="button" x-on:click="pilihPenerima(user)"
                                    class="block w-full px-3 py-2 text-left text-sm hover:bg-slate-50">
                                <span x-text="user.name"></span>
                                <span class="block text-xs text-slate-400" x-text="user.email"></span>
                            </button>
                        </template>
                    </div>
                    <p x-show="selectedUser" class="mt-1.5 text-xs text-emerald-600">
                        Dipilih: <span x-text="selectedUser?.name"></span>
                    </p>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Penerimaan</label>
                    <input type="date" x-model="tanggalSerah"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none">
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan</label>
                    <textarea x-model="catatan" rows="2" placeholder="cth. diterima dalam keadaan baik"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-slate-500 focus:outline-none"></textarea>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Foto Bukti Serah Terima (<span x-text="fotos.length"></span> Foto)
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

                <p x-show="error" x-text="error" class="mb-3 text-sm text-red-600"></p>

                <button type="submit" :disabled="submitting"
                        class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-50">
                    <span x-show="!submitting">Serahkan Aset</span>
                    <span x-show="submitting">Menyimpan...</span>
                </button>
            </form>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        function serahkanAsetForm(asetId) {
            return {
                open: false,
                tujuan: 'karyawan',
                query: '',
                results: [],
                showResults: false,
                selectedUser: null,
                tanggalSerah: new Date().toISOString().slice(0, 10),
                catatan: '',
                fotos: [],
                fotoPreviews: [],
                submitting: false,
                error: null,

                setTujuan(t) {
                    this.tujuan = t;
                    this.selectedUser = null;
                    this.query = '';
                    this.results = [];
                },

                async cariPenerima() {
                    if (this.query.length < 1) { this.results = []; return; }
                    const url = `/inventaris/aset/${asetId}/cari-penerima?tujuan=${this.tujuan}&q=${encodeURIComponent(this.query)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    this.results = res.ok ? await res.json() : [];
                },

                pilihPenerima(user) {
                    this.selectedUser = user;
                    this.query = user.name;
                    this.showResults = false;
                },

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

                    if (!this.selectedUser) { this.error = 'Pilih penerima dulu.'; return; }
                    if (this.fotos.length < 1) { this.error = 'Minimal 1 foto bukti serah terima.'; return; }

                    const fd = new FormData();
                    fd.append('tujuan', this.tujuan);
                    fd.append('user_id', this.selectedUser.id);
                    fd.append('tanggal_serah', this.tanggalSerah);
                    fd.append('catatan_serah', this.catatan);
                    this.fotos.forEach(f => fd.append('foto_serah[]', f));

                    this.submitting = true;
                    const res = await fetch(`/inventaris/aset/${asetId}/serahkan`, {
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
                        this.error = data?.message || 'Gagal menyerahkan aset.';
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