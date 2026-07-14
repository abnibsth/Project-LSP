<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Penggajian PT Nikel Indonesia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Card Login --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            {{-- Header Card --}}
            <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-8 py-8 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-2xl">PN</span>
                </div>
                <h1 class="text-white text-xl font-bold">PT Nikel Indonesia</h1>
                <p class="text-blue-100 text-sm mt-1">Sistem Penggajian Karyawan</p>
            </div>

            {{-- Form Login --}}
            <div class="px-8 py-8">
                <h2 class="text-gray-800 text-lg font-semibold mb-6">Masuk ke Sistem</h2>

                {{-- Pesan Error Login --}}
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-4 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    {{-- Field Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
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
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                {{ $errors->has('email') ? 'border-red-400 bg-red-50' : '' }}"
                        >
                    </div>

                    {{-- Field Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="Masukkan password"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    {{-- Checkbox Ingat Saya --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600">
                        <label for="remember" class="text-sm text-gray-600">Ingat saya di perangkat ini</label>
                    </div>

                    {{-- Tombol Login --}}
                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors text-sm mt-2"
                    >
                        Masuk ke Sistem
                    </button>
                </form>

                {{-- Info akun demo --}}
                <div class="mt-6 p-4 bg-gray-50 rounded-lg text-xs text-gray-500 space-y-1">
                    <p class="font-medium text-gray-600">Akun Demo:</p>
                    <p>👔 Admin: admin@ptnikel.com / password</p>
                    <p>👷 Karyawan: budi@ptnikel.com / password</p>
                </div>
            </div>
        </div>

        <p class="text-center text-blue-200 text-xs mt-4">
            © {{ date('Y') }} PT Nikel Indonesia — Sistem Penggajian v1.3
        </p>
    </div>

</body>
</html>
