@extends('layouts.app')

@section('title', 'Data Cabang')

@section('content')
    {{-- Data ($cabangs, $isAdmin) sudah disiapkan sepenuhnya di controller. Jangan taruh closure/map PHP di dalam @json() di sini. --}}

    <div
        x-data="cabangPage({ cabangs: @json($cabangs), isAdmin: @json($isAdmin ?? false) })"
        x-init="init()"
        class="max-w-6xl mx-auto"
    >
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-900">Data Cabang</h1>
            <p class="text-sm text-slate-500">Kelola data seluruh cabang perusahaan.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

            {{-- Search + tombol tambah --}}
            <div class="mb-4 flex flex-col gap-2 sm:flex-row">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Cari nama atau alamat cabang..."
                        class="w-full rounded-lg border border-slate-200 py-2 pl-9 pr-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900/10"
                    >
                </div>

                @if($isAdmin ?? false)
                    <a
                        href="{{ route('cabang.create') }}"
                        class="flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-800"
                    >
                        + Tambah Cabang
                    </a>
                @endif
            </div>

            <p class="mb-4 text-sm text-slate-500">
                Total cabang ada
                <span class="font-semibold text-slate-900" x-text="filtered().length"></span>
            </p>

            {{-- Kosong --}}
            <template x-if="filtered().length === 0">
                <p class="py-8 text-center text-sm text-slate-400">Tidak ada cabang yang cocok dengan pencarian ini.</p>
            </template>

            {{-- List --}}
            <template x-if="filtered().length > 0">
                <div>
                    <div class="divide-y divide-slate-100">
                        <template x-for="cabang in paginated()" :key="cabang.id">
                            <div class="flex items-center justify-between gap-3 py-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-medium text-slate-700" x-text="initials(cabang.nama)"></div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-slate-900" x-text="cabang.nama"></p>
                                        <p class="truncate text-xs text-slate-500" x-text="cabang.alamat || 'Alamat belum diisi'"></p>
                                    </div>
                                </div>
                                <div class="flex flex-shrink-0 items-center gap-3">
                                    <span class="hidden text-xs text-slate-500 sm:inline" x-show="cabang.telepon" x-text="cabang.telepon"></span>
                                    <a
                                        :href="cabang.link"
                                        target="_blank"
                                        rel="noopener"
                                        x-show="cabang.link"
                                        class="text-xs text-slate-500 hover:text-slate-900 underline"
                                    >Lihat lokasi</a>
                                    <template x-if="isAdmin">
                                        <span class="flex items-center gap-3">
                                            <a :href="'{{ url('cabang') }}/' + cabang.id + '/edit'" class="text-xs text-slate-500 hover:text-slate-900">Edit</a>
                                            <button type="button" @click="cabangToDelete = cabang" class="text-xs text-red-600 hover:text-red-700">Hapus</button>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4 flex items-center justify-between text-sm text-slate-500" x-show="totalPages() > 1">
                        <span>Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages()"></span></span>
                        <div class="flex gap-1">
                            <button
                                type="button"
                                @click="currentPage = Math.max(1, currentPage - 1)"
                                :disabled="currentPage === 1"
                                class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50 disabled:opacity-40"
                            >Sebelumnya</button>
                            <button
                                type="button"
                                @click="currentPage = Math.min(totalPages(), currentPage + 1)"
                                :disabled="currentPage === totalPages()"
                                class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50 disabled:opacity-40"
                            >Berikutnya</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Modal konfirmasi hapus --}}
        <div
            x-show="cabangToDelete"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
        >
            <div class="max-h-[85vh] w-full max-w-xs overflow-y-auto rounded-xl bg-white p-4" @click.outside="cabangToDelete = null">
                <h2 class="mb-1 text-sm font-semibold text-slate-900">Hapus cabang?</h2>
                <p class="mb-4 text-xs text-slate-500">
                    <span class="font-medium text-slate-700" x-text="cabangToDelete?.nama"></span>
                    akan dihapus permanen dan tidak bisa dikembalikan.
                </p>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="cabangToDelete = null"
                        :disabled="deleting"
                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm hover:bg-slate-50 disabled:opacity-50"
                    >Batal</button>
                    <button
                        type="button"
                        @click="confirmDelete()"
                        :disabled="deleting"
                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm text-white hover:bg-red-700 disabled:opacity-50"
                        x-text="deleting ? 'Menghapus...' : 'Ya, hapus'"
                    ></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cabangPage', ({ cabangs, isAdmin }) => ({
                cabangs,
                isAdmin,
                search: '',
                currentPage: 1,
                itemsPerPage: 10,
                cabangToDelete: null,
                deleting: false,

                init() {
                    this.$watch('search', () => (this.currentPage = 1));
                },

                initials(nama) {
                    return (nama || '')
                        .split(' ')
                        .map((w) => w[0])
                        .slice(0, 2)
                        .join('')
                        .toUpperCase();
                },

                filtered() {
                    const q = this.search.toLowerCase().trim();
                    return this.cabangs.filter((c) => {
                        return (
                            (c.nama || '').toLowerCase().includes(q) ||
                            (c.alamat || '').toLowerCase().includes(q)
                        );
                    });
                },

                totalPages() {
                    return Math.max(1, Math.ceil(this.filtered().length / this.itemsPerPage));
                },

                paginated() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filtered().slice(start, start + this.itemsPerPage);
                },

                async confirmDelete() {
                    if (!this.cabangToDelete) return;
                    this.deleting = true;

                    try {
                        const res = await fetch(`{{ url('cabang') }}/${this.cabangToDelete.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                Accept: 'application/json',
                            },
                        });

                        if (!res.ok) throw new Error('Gagal menghapus');

                        this.cabangs = this.cabangs.filter((c) => c.id !== this.cabangToDelete.id);
                        if (window.toastr) toastr.success('Cabang berhasil dihapus.');
                        this.cabangToDelete = null;
                    } catch (e) {
                        if (window.toastr) toastr.error('Gagal menghapus cabang. Coba lagi.');
                        this.cabangToDelete = null;
                    } finally {
                        this.deleting = false;
                    }
                },
            }));
        });
    </script>
@endpush