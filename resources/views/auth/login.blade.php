<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Penggajian PT Nikel Indonesia</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas-parchment flex items-center justify-center p-4">

    <div class="w-full max-w-sm">
        {{-- Card Login (Apple Configurator style - 18px radius, no shadow) --}}
        <div class="bg-white rounded-lg border border-hairline overflow-hidden shadow-none">

            {{-- Header Card --}}
            <div class="px-8 pt-8 text-center">
                <img src="{{ asset('logo.png') }}" class="w-14 h-14 rounded-lg object-contain mx-auto mb-3 bg-white border border-hairline p-2" alt="Logo">
                <h1 class="text-ink text-lg font-bold tracking-tight">PT Nikel Indonesia</h1>
                <p class="text-ink-muted-48 text-xs mt-0.5">Sistem Penggajian Karyawan</p>
            </div>

            {{-- Form Login --}}
            <div class="px-8 py-6">
                <h2 class="text-ink text-sm font-semibold mb-4">Masuk ke Sistem</h2>

                {{-- Pesan Error Login --}}
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-sm px-4 py-2.5 mb-4 text-xs font-medium">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    {{-- Field Email --}}
                    <div>
                        <label for="email" class="block text-xs font-medium text-ink-muted-80 mb-1">
                            Alamat Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="contoh@ptnikel.com"
                            class="w-full px-4 py-2 border border-hairline rounded-pill text-xs focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all
                                {{ $errors->has('email') ? 'border-red-400 bg-red-50' : '' }}"
                        >
                    </div>

                    {{-- Field Password --}}
                    <div>
                        <label for="password" class="block text-xs font-medium text-ink-muted-80 mb-1">
                            Password
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="Masukkan password"
                            class="w-full px-4 py-2 border border-hairline rounded-pill text-xs focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                        >
                    </div>

                    {{-- Checkbox Ingat Saya --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember" class="w-3.5 h-3.5 rounded-sm border-hairline text-primary focus:ring-primary">
                        <label for="remember" class="text-xs text-ink-muted-80">Ingat saya di perangkat ini</label>
                    </div>

                    {{-- Tombol Login --}}
                    <button
                        type="submit"
                        class="w-full bg-primary hover:bg-primary-focus text-white font-medium py-2 rounded-pill text-xs transition-all active:scale-95 mt-1"
                    >
                        Masuk ke Sistem
                    </button>
                </form>

                {{-- Info akun demo --}}
                <div class="mt-5 p-3.5 bg-canvas-parchment border border-hairline rounded-lg text-[10px] text-ink-muted-80 space-y-1">
                    <p class="font-bold text-ink mb-1">Akun Demo:</p>
                    <p>👔 Admin: admin@ptnikel.com / password</p>
                    <p>👷 Karyawan: budi@ptnikel.com / password</p>
                </div>
            </div>
        </div>

        <p class="text-center text-ink-muted-48 text-[10px] mt-4">
            © {{ date('Y') }} PT Nikel Indonesia — Sistem Penggajian v1.3
        </p>
    </div>

</body>
</html>
