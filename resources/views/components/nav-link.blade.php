@props(['href', 'active' => false])

<a href="{{ $href }}" wire:navigate
   class="mb-1 flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
    {{ $slot }}
</a>