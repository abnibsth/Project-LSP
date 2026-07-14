<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Penggajian') — PT Nikel Indonesia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    {{-- Navigasi Atas --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo & Nama Perusahaan --}}
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">PN</span>
                    </div>
                    <span class="font-semibold text-gray-900">PT Nikel Indonesia</span>
                    <span class="text-gray-400">|</span>
                    <span class="text-sm text-gray-500">Sistem Penggajian</span>
                </div>

                {{-- Info User yang sedang login + Tombol Logout --}}
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-gray-500 hover:text-red-600 transition-colors px-3 py-1.5 rounded-md hover:bg-red-50">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex min-h-screen">
        {{-- Sidebar Navigasi Kiri --}}
        <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0">
            <nav class="p-4 space-y-1">
                @if(auth()->user()->isAdmin())
                    {{-- Menu Admin --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">Menu Admin</p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        🏠 Dashboard
                    </a>
                    <a href="{{ route('admin.karyawan.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.karyawan.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        👥 Karyawan
                    </a>
                    <a href="{{ route('admin.komponen-gaji.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.komponen-gaji.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        💰 Komponen Gaji
                    </a>
                    <a href="{{ route('admin.aturan-absensi.edit') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.aturan-absensi.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        ⚙️ Aturan Absensi
                    </a>
                    <a href="{{ route('admin.absensi.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.absensi.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        📋 Rekap Absensi
                    </a>
                    <a href="{{ route('admin.payroll.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.payroll.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        🔄 Proses Payroll
                    </a>
                    <a href="{{ route('admin.slip-gaji.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.slip-gaji.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        📄 Slip Gaji
                    </a>

                    <div class="border-t border-gray-100 my-2"></div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Laporan</p>
                    <a href="{{ route('admin.laporan.payroll') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.laporan.payroll*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        📊 Laporan Payroll
                    </a>
                    <a href="{{ route('admin.laporan.absensi') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('admin.laporan.absensi*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        📈 Laporan Absensi
                    </a>
                @else
                    {{-- Menu Karyawan --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">Menu Karyawan</p>

                    <a href="{{ route('karyawan.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('karyawan.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        🏠 Dashboard
                    </a>
                    <a href="{{ route('karyawan.absensi.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('karyawan.absensi.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        ✅ Absensi
                    </a>
                    <a href="{{ route('karyawan.slip-gaji.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('karyawan.slip-gaji.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        📄 Slip Gaji
                    </a>
                    <a href="{{ route('karyawan.profil.edit') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                            {{ request()->routeIs('karyawan.profil.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        👤 Profil Saya
                    </a>
                @endif
            </nav>
        </aside>

        {{-- Konten Halaman Utama --}}
        <main class="flex-1 p-6">
            {{-- Notifikasi Sukses / Error / Info --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center gap-2">
                    <span>❌</span> {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg px-4 py-3 flex items-center gap-2">
                    <span>ℹ️</span> {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
