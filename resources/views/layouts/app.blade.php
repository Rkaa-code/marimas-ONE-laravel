<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inventaris') - Marimas One</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased">
    <div x-data="{ sidebarOpen: false }" x-on:sidebar-toggle.window="sidebarOpen = true" class="h-screen bg-white flex overflow-hidden">

        {{-- overlay mobile --}}
        <div x-show="sidebarOpen" x-cloak x-on:click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

        {{-- sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed lg:sticky top-0 left-0 z-50 flex h-screen w-64 shrink-0 flex-col bg-white transition-transform duration-300 lg:translate-x-0">
            <div class="flex h-18 shrink-0 items-center justify-between px-6">
                <span class="text-lg font-bold text-slate-900">Marimas One</span>
                <button x-on:click="sidebarOpen = false" class="text-slate-400 lg:hidden">
                    <x-icon.x class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-icon.layout-dashboard class="h-[18px] w-[18px]" />
                    Dashboard
                </x-nav-link>

                <x-nav-link :href="route('inventaris.aset.index')" :active="request()->routeIs('inventaris.aset.*')">
                    <x-icon.package class="h-[18px] w-[18px]" />
                    Aset
                </x-nav-link>

                <x-nav-link :href="route('cabang.index')" :active="request()->routeIs('cabang.index')">
                    <x-icon.package class="h-[18px] w-[18px]" />
                    Cabang
                </x-nav-link>

                <div x-data="{ open: {{ request()->routeIs('inventaris.master.*') ? 'true' : 'false' }} }" class="mb-1">
                    <button x-on:click="open = !open"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('inventaris.master.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        <x-icon.database class="h-[18px] w-[18px]" />
                        <span class="flex-1 text-left">Master Data</span>
                        <x-icon.chevron-down class="h-4 w-4 transition-transform duration-200" x-bind:class="open && 'rotate-180'" />
                    </button>

                    <div x-show="open" x-cloak class="ml-4 mt-1 flex flex-col gap-1 border-l border-slate-200 pl-3">
                        <x-nav-sublink :href="route('inventaris.master.jenis-aset.index')" :active="request()->routeIs('inventaris.master.jenis-aset.*')">
                            Jenis Aset
                        </x-nav-sublink>
                        <x-nav-sublink :href="route('inventaris.master.supplier.index')" :active="request()->routeIs('inventaris.master.supplier.*')">
                            Supplier
                        </x-nav-sublink>
                        <x-nav-sublink :href="route('inventaris.master.kelengkapan.index')" :active="request()->routeIs('inventaris.master.kelengkapan.*')">
                            Kelengkapan
                        </x-nav-sublink>
                    </div>
                </div>
            </nav>

            <div class="shrink-0 border-t border-slate-100 p-3">
                <div class="flex items-center gap-3 rounded-lg px-3 py-2.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white">
                        {{ strtoupper(substr(session('dummy_user_name', 'Admin'), 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-800">{{ session('dummy_user_name', 'Admin') }}</p>
                        <p class="truncate text-xs text-slate-400">{{ session('dummy_user_email', 'admin@marimasone.test') }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-slate-700" title="Keluar">
                            <x-icon.log-out class="h-[18px] w-[18px]" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- main area --}}
        <div class="flex h-screen min-w-0 flex-1 flex-col overflow-y-auto">
            <header class="sticky top-0 z-30 flex h-18 min-h-18 shrink-0 items-center justify-between bg-white px-4 md:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" x-on:click="window.dispatchEvent(new CustomEvent('sidebar-toggle'))" class="text-slate-600 lg:hidden">
                        <x-icon.menu class="h-[22px] w-[22px]" />
                    </button>
                    <h1 class="hidden text-xl font-bold text-slate-900 sm:block">@yield('title', 'Inventaris')</h1>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-8">
                <div class="min-h-[calc(100vh-6.5rem)] rounded-3xl bg-zinc-100 p-4 md:p-8">
                    @include('components.flash')

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- PENTING: script custom (@stack('scripts')) HARUS dijalankan sebelum @livewireScripts, --}}
    {{-- karena Livewire 3 membawa Alpine.js sendiri dan otomatis Alpine.start(). --}}
    {{-- Kalau urutannya kebalik, komponen Alpine custom (mis. cabangPage) belum ke-define saat Alpine mulai jalan. --}}
    @stack('scripts')
    @livewireScripts
</body>
</html>