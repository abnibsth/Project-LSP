<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Penggajian PT Nikel Indonesia</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-shell font-sans antialiased text-ink">

    <div class="w-full max-w-[26rem]">
        <div class="text-center mb-7">
            <img src="{{ asset('logo.png') }}" class="w-14 h-14 rounded-[14px] object-contain mx-auto mb-4 bg-white border border-hairline p-2 shadow-[var(--shadow-card)]" alt="Logo PT Nikel Indonesia">
            <h1 class="text-[1.25rem] font-semibold tracking-tight text-ink">PT Nikel Indonesia</h1>
            <p class="text-ink-muted-48 text-sm mt-1">Sistem Penggajian Karyawan</p>
        </div>

        <div class="login-card">
            <h2 class="text-[1.05rem] font-semibold tracking-tight text-ink mb-0.5">Masuk</h2>
            <p class="text-ink-muted-48 text-sm mb-5">Gunakan akun perusahaan Anda.</p>

            @if($errors->any())
                <div class="alert alert-error">
                    <span class="alert-icon" aria-hidden="true">!</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="nama@ptnikel.com"
                        class="form-input form-input-pill {{ $errors->has('email') ? 'is-invalid' : '' }}"
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

                <div class="flex items-center gap-2 pt-0.5">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-hairline text-primary focus:ring-primary">
                    <label for="remember" class="text-sm text-ink-muted-80">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-primary w-full !py-3 mt-1">
                    Masuk
                </button>
            </form>

            <div class="login-demo">
                <p class="font-semibold text-ink mb-1.5">Akun demo</p>
                <p>Admin · admin@ptnikel.com / password</p>
                <p>Karyawan · budi@ptnikel.com / password</p>
            </div>
        </div>

        <p class="text-center text-ink-muted-48 text-xs mt-6">
            © {{ date('Y') }} PT Nikel Indonesia
        </p>
    </div>

</body>
</html>
