<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Marimas One</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])A
</head>
<body class="antialiased">
    <div class="flex min-h-screen items-center justify-center bg-zinc-100 px-4">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900">
                    <x-icon.package class="h-6 w-6 text-white" />
                </div>
                <h1 class="text-xl font-bold text-slate-900">Marimas One</h1>
                <p class="mt-1 text-sm text-slate-500">Masuk untuk melanjutkan ke dashboard.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Dummy login: tidak ada pengecekan kredensial sungguhan, langsung diarahkan ke dashboard. --}}
                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <div class="relative">
                            <x-icon.user class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input id="email" type="email" name="email" value="admin@marimasone.test" required
                                   class="w-full rounded-lg border border-slate-300 py-2.5 pl-9 pr-3 text-sm focus:border-slate-500 focus:outline-none"
                                   placeholder="nama@perusahaan.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                        <div class="relative">
                            <x-icon.lock class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input id="password" type="password" name="password" value="password" required
                                   class="w-full rounded-lg border border-slate-300 py-2.5 pl-9 pr-3 text-sm focus:border-slate-500 focus:outline-none"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-slate-600">
                            <input type="checkbox" name="remember" class="rounded border-slate-300">
                            Ingat saya
                        </label>
                        <span class="text-slate-400">Lupa password?</span>
                    </div>

                    <button type="submit"
                            class="w-full rounded-lg bg-slate-900 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                Halaman ini masih dummy — form akan langsung masuk ke dashboard tanpa validasi kredensial sungguhan.
            </p>
        </div>
    </div>
</body>
</html>