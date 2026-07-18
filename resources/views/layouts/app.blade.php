<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Penggajian') — PT Nikel Indonesia</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('icon/font/flaticon.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas-parchment text-ink antialiased">

    <div class="flex h-screen overflow-hidden relative">
        {{-- Backdrop Overlay for Mobile --}}
        <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden transition-opacity duration-300"></div>        {{-- Sidebar Navigasi Kiri (Apple macOS Sidebar - Drawer di Mobile) --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-[#1d1d1f] border-r border-gray-800 flex-shrink-0 shadow-none transform -translate-x-full md:translate-x-0 md:relative md:flex md:flex-col transition-transform duration-300 ease-in-out h-screen">
            
            {{-- Header Sidebar (Logo & Nama Perusahaan) --}}
            <div class="p-5 border-b border-gray-800 bg-[#1d1d1f] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" class="w-6 h-6 rounded-sm object-contain bg-white p-0.5" alt="Logo">
                    <div>
                        <span class="font-bold text-sm tracking-tight block text-white">PT Nikel</span>
                        <span class="text-[10px] text-gray-400 block">Sistem Penggajian</span>
                    </div>
                </div>
                <button id="sidebarClose" class="md:hidden text-gray-400 hover:text-white cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Links Menu --}}
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                @if(auth()->user()->isAdmin())
                    {{-- Menu Admin --}}
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2.5 px-3">Menu Admin</p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-009-dashboard text-sm"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.karyawan.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.karyawan.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-025-members text-sm"></i> Karyawan
                    </a>
                    <a href="{{ route('admin.komponen-gaji.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.komponen-gaji.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-002-billing text-sm"></i> Komponen Gaji
                    </a>
                    <a href="{{ route('admin.aturan-absensi.edit') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.aturan-absensi.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-006-configuration text-sm"></i> Aturan Absensi
                    </a>
                    <a href="{{ route('admin.absensi.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.absensi.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-005-checklist text-sm"></i> Rekap Absensi
                    </a>
                    <a href="{{ route('admin.payroll.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.payroll.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-032-refresh-page text-sm"></i> Proses Payroll
                    </a>
                    <a href="{{ route('admin.slip-gaji.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.slip-gaji.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-013-files text-sm"></i> Slip Gaji
                    </a>

                    <div class="border-t border-gray-800 my-3"></div>
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2.5 px-3">Laporan</p>
                    <a href="{{ route('admin.laporan.payroll') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.laporan.payroll*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-040-stats text-sm"></i> Laporan Payroll
                    </a>
                    <a href="{{ route('admin.laporan.absensi') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('admin.laporan.absensi*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-041-stats text-sm"></i> Laporan Absensi
                    </a>
                @else
                    {{-- Menu Karyawan --}}
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2.5 px-3">Menu Karyawan</p>

                    <a href="{{ route('karyawan.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('karyawan.dashboard') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-009-dashboard text-sm"></i> Dashboard
                    </a>
                    <a href="{{ route('karyawan.absensi.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('karyawan.absensi.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-005-checklist text-sm"></i> Absensi
                    </a>
                    <a href="{{ route('karyawan.slip-gaji.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('karyawan.slip-gaji.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-013-files text-sm"></i> Slip Gaji
                    </a>
                    <a href="{{ route('karyawan.profil.edit') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-pill text-xs font-medium transition-all active:scale-95
                            {{ request()->routeIs('karyawan.profil.*') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-[#272729] hover:text-white' }}">
                        <i class="flaticon-030-profile text-sm"></i> Profil Saya
                    </a>
                @endif
            </nav>

            {{-- Footer Sidebar (User Info & Tombol Logout) --}}
            <div class="p-4 border-t border-gray-800 bg-[#1d1d1f] flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="truncate mr-2">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-gray-400 capitalize truncate">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit"
                            class="text-xs text-red-400 hover:text-red-300 hover:bg-gray-800 transition-colors px-2 py-1 rounded-sm active:scale-95 font-semibold cursor-pointer">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Konten Halaman Utama (Apple Clean Canvas) --}}
        <div class="flex-1 flex flex-col min-w-0 bg-canvas-parchment h-screen overflow-hidden">
            
            {{-- Mobile Top Navbar (Hanya muncul di mobile < md) --}}
            <header class="md:hidden flex items-center justify-between p-3.5 bg-white border-b border-hairline sticky top-0 z-20">
                <div class="flex items-center gap-2.5">
                    <button id="sidebarToggle" class="text-ink hover:text-primary focus:outline-none active:scale-95 transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <img src="{{ asset('logo.png') }}" class="w-5 h-5 rounded-sm object-contain" alt="Logo">
                    <span class="font-bold text-xs tracking-tight text-ink">PT Nikel</span>
                </div>
                <div class="text-[10px] font-medium text-ink-muted-80">
                    {{ auth()->user()->name }}
                </div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto bg-canvas-parchment">
                {{-- Notifikasi Sukses / Error / Info --}}
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-sm px-4 py-2.5 flex items-center gap-2 text-xs font-medium">
                        <span>✅</span> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-sm px-4 py-2.5 flex items-center gap-2 text-xs font-medium">
                        <span>❌</span> {{ session('error') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-sm px-4 py-2.5 flex items-center gap-2 text-xs font-medium">
                        <span>ℹ️</span> {{ session('info') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Script JavaScript untuk Responsivitas Sidebar Drawer --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('sidebarClose');
            const backdrop = document.getElementById('sidebarBackdrop');
            const sidebar = document.getElementById('sidebar');

            if (toggleBtn && sidebar && backdrop) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                });
            }

            if (closeBtn && sidebar && backdrop) {
                closeBtn.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                });
            }

            if (backdrop && sidebar) {
                backdrop.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>
