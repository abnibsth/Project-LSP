<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Penggajian PT Nikel Indonesia</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas-parchment flex items-center justify-center p-4 font-sans antialiased text-ink">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="{{ asset('logo.png') }}" class="w-16 h-16 rounded-lg object-contain mx-auto mb-4 bg-white border border-hairline p-2" alt="Logo">
            <h1 class="typography-tagline text-ink tracking-tight">PT Nikel Indonesia</h1>
            <p class="text-ink-muted-48 text-sm mt-1">Sistem Penggajian Karyawan</p>
        </div>

        <div class="ui-card ui-card-pad">
            <h2 class="typography-body-strong text-ink mb-1">Masuk ke Sistem</h2>
            <p class="text-ink-muted-48 text-sm mb-6">Gunakan akun perusahaan Anda untuk melanjutkan.</p>

            @if($errors->any())
                <div class="alert alert-error">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="form-label">Alamat Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="contoh@ptnikel.com"
                        class="form-input form-input-pill {{ $errors->has('email') ? 'border-red-400 bg-red-50' : '' }}"
                    >
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                        class="form-input form-input-pill"
                    >
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded-sm border-hairline text-primary focus:ring-primary">
                    <label for="remember" class="text-sm text-ink-muted-80">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="mt-6 p-4 bg-canvas-parchment border border-hairline rounded-lg text-xs text-ink-muted-80 space-y-1.5">
                <p class="font-semibold text-ink mb-1.5">Akun Demo</p>
                <p>Admin: admin@ptnikel.com / password</p>
                <p>Karyawan: budi@ptnikel.com / password</p>
            </div>
        </div>

        <p class="text-center text-ink-muted-48 text-xs mt-6">
            © {{ date('Y') }} PT Nikel Indonesia — Sistem Penggajian
        </p>
    </div>

</body>
</html>
