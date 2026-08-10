@props(['href', 'active' => false])

<a href="{{ $href }}" wire:navigate
   class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition {{ $active ? 'bg-slate-100 font-medium text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
    {{ $slot }}
</a>