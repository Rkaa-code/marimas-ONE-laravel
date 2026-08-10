<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventaris') - Marimas One</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">
        <aside class="w-64 shrink-0 bg-slate-900 text-slate-200">
            <div class="px-6 py-5 border-b border-slate-800">
                <span class="text-lg font-semibold text-white">Marimas One</span>
                <p class="text-xs text-slate-400">Inventaris</p>
            </div>
            <nav class="px-3 py-4 space-y-1">
                <a href="{{ route('inventaris.aset.index') }}"
                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('inventaris.aset.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Aset
                </a>

                <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Master Data</p>

                <a href="{{ route('inventaris.master.jenis-aset.index') }}"
                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('inventaris.master.jenis-aset.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Jenis Aset
                </a>
                <a href="{{ route('inventaris.master.supplier.index') }}"
                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('inventaris.master.supplier.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Supplier
                </a>
                <a href="{{ route('inventaris.master.kelengkapan.index') }}"
                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('inventaris.master.kelengkapan.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Kelengkapan
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="mx-auto max-w-6xl">
                @include('components.flash')

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
