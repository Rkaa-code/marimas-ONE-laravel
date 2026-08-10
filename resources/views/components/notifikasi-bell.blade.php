@php
    $levelDot = [
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-red-500',
        'info' => 'bg-sky-500',
    ];
@endphp

<div x-data="notifikasiBell()" x-init="init()" class="relative">
    <button type="button" x-on:click="toggle()" class="relative text-slate-500 hover:text-slate-800" title="Notifikasi">
        <x-icon.bell class="h-[22px] w-[22px]" />
        <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 9 ? '9+' : unreadCount"
              class="absolute -top-1.5 -right-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"></span>
    </button>

    <div x-show="open" x-cloak x-on:click.outside="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute right-0 z-50 mt-3 w-80 max-w-[90vw] overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xl sm:w-96">

        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <p class="text-sm font-semibold text-slate-800">Notifikasi</p>
            <button type="button" x-show="unreadCount > 0" x-on:click="bacaSemua()" class="text-xs font-medium text-slate-400 hover:text-slate-700">
                Tandai semua dibaca
            </button>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <template x-if="items.length === 0">
                <p class="px-4 py-8 text-center text-sm text-slate-400">Belum ada notifikasi.</p>
            </template>

            <template x-for="item in items" :key="item.id">
                <a :href="item.url ?? '#'" x-on:click="baca(item)"
                   class="flex gap-3 border-b border-slate-50 px-4 py-3 text-left transition last:border-b-0 hover:bg-slate-50"
                   :class="!item.read && 'bg-slate-50/70'">
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full" :class="dot(item.level)"></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-medium text-slate-800" x-text="item.title"></span>
                        <span class="mt-0.5 block text-xs leading-relaxed text-slate-500" x-text="item.message"></span>
                        <span class="mt-1 block text-[11px] text-slate-400" x-text="item.waktu"></span>
                    </span>
                    <span x-show="!item.read" class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-500"></span>
                </a>
            </template>
        </div>

        <div class="border-t border-slate-100 px-4 py-2.5 text-center">
            <a href="{{ route('notifikasi.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800">Lihat semua notifikasi</a>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function notifikasiBell() {
                return {
                    open: false,
                    unreadCount: 0,
                    items: [],
                    poller: null,
                    init() {
                        this.fetchTerbaru();
                        this.poller = setInterval(() => this.fetchTerbaru(), 20000);
                    },
                    toggle() {
                        this.open = !this.open;
                        if (this.open) this.fetchTerbaru();
                    },
                    dot(level) {
                        return {
                            success: 'bg-emerald-500',
                            warning: 'bg-amber-500',
                            danger: 'bg-red-500',
                            info: 'bg-sky-500',
                        }[level] ?? 'bg-sky-500';
                    },
                    async fetchTerbaru() {
                        try {
                            const res = await fetch('{{ route('notifikasi.terbaru') }}', {
                                headers: { 'Accept': 'application/json' },
                            });
                            if (!res.ok) return;
                            const data = await res.json();
                            this.unreadCount = data.unread_count;
                            this.items = data.notifikasi;
                        } catch (e) {
                            // diem-diem aja, biar gak ganggu, coba lagi di polling berikutnya
                        }
                    },
                    async baca(item) {
                        if (item.read) return;
                        item.read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        try {
                            await fetch(`/notifikasi/${item.id}/baca`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                            });
                        } catch (e) {}
                    },
                    async bacaSemua() {
                        this.items.forEach(i => i.read = true);
                        this.unreadCount = 0;
                        try {
                            await fetch('{{ route('notifikasi.baca-semua') }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                            });
                        } catch (e) {}
                    },
                };
            }
        </script>
    @endpush
@endonce
