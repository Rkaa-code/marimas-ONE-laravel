@props(['title', 'subtitle' => null, 'fotos' => []])

<div x-data="{ open: false }" x-cloak class="inline-block">
    <button type="button" x-on:click="open = true"
            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900">
        <x-icon.eye class="h-3.5 w-3.5" />
        Lihat Foto
    </button>

    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/50" x-on:click="open = false"></div>

        <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-slate-900 truncate">{{ $title }}</h2>
                    @if ($subtitle)
                        <p class="text-sm text-slate-500 truncate">{{ $subtitle }}</p>
                    @endif
                </div>
                <button type="button" x-on:click="open = false" class="shrink-0 text-slate-400 hover:text-slate-600">
                    <x-icon.x class="h-5 w-5" />
                </button>
            </div>

            @if (count($fotos) === 0)
                <p class="py-8 text-center text-sm text-slate-400">Tidak ada foto.</p>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($fotos as $foto)
                        <a href="{{ $foto }}" target="_blank" rel="noopener"
                           class="group block aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                            <img src="{{ $foto }}" alt="{{ $title }}"
                                 class="h-full w-full object-cover transition group-hover:scale-105">
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
