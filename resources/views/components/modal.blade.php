@props(['name', 'title'])

<div x-data="{ open: false }"
     x-on:open-modal-{{ $name }}.window="open = true"
     x-on:close-modal-{{ $name }}.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div x-show="open" x-on:click.outside="open = false"
         class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
            <button type="button" x-on:click="open = false" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>

        {{ $slot }}
    </div>
</div>
