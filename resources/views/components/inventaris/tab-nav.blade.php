@props(['active'])

@php
    $tabs = [
        'aset' => [
            'label' => 'Aset',
            'route' => 'inventaris.aset.index',
            'count' => \App\Models\Aset::count(),
            'icon' => 'package',
        ],
        'penanganan-aset' => [
            'label' => 'Penanganan Aset',
            'route' => 'inventaris.penanganan-aset.index',
            'count' => \App\Models\AsetPenanganan::whereIn('status', ['menunggu_terima', 'sedang_diperbaiki'])->count(),
            'icon' => 'wrench',
        ],
        'foto-aset' => [
            'label' => 'Foto Aset',
            'route' => 'inventaris.foto-aset.index',
            'count' => null,
            'icon' => 'image',
        ],
    ];
@endphp

<div class="mb-6">
    <p class="text-sm text-slate-500">Kelola aset IT</p>

    <nav class="mt-2 border-b border-slate-200">
        <ul class="flex items-center gap-6 overflow-x-auto">
            @foreach ($tabs as $key => $tab)
                <li class="shrink-0">
                    <a href="{{ route($tab['route']) }}"
                       class="flex items-center gap-2 pb-3 text-sm whitespace-nowrap border-b-2 -mb-px transition-colors {{ $active === $key ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                        <x-dynamic-component :component="'icon.' . $tab['icon']" class="h-4 w-4" />
                        {{ $tab['label'] }}
                        @if (!is_null($tab['count']))
                            <span class="text-xs px-1.5 py-0.5 rounded-full {{ $active === $key ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $tab['count'] }}
                            </span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
