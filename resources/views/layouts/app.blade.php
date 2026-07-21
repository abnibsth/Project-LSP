<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Penggajian') — PT Nikel Indonesia</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('icon/font/flaticon.css') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas-parchment text-ink antialiased font-sans">

    <div class="flex h-screen overflow-hidden relative">
        <div id="sidebarBackdrop" class="fixed inset-0 bg-surface-black/40 z-30 hidden md:hidden transition-opacity duration-300"></div>

        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-ink flex-shrink-0 transform -translate-x-full md:translate-x-0 md:relative md:flex md:flex-col transition-transform duration-300 ease-in-out h-screen">
            <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('logo.png') }}" class="w-7 h-7 rounded-sm object-contain bg-white p-0.5 shrink-0" alt="Logo">
                    <div class="min-w-0">
                        <span class="font-semibold text-sm tracking-tight block text-white truncate">PT Nikel</span>
                        <span class="text-[10px] text-body-muted block">Sistem Penggajian</span>
                    </div>
                </div>
                <button id="sidebarClose" type="button" class="md:hidden text-body-muted hover:text-white cursor-pointer p-1 rounded-sm active:scale-95" aria-label="Tutup menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto scroll-soft p-4 space-y-1">
                @if(auth()->user()->isAdmin())
                    <p class="sidebar-section-label">Menu Admin</p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <i class="flaticon-009-dashboard text-sm"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.karyawan.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.karyawan.*') ? 'is-active' : '' }}">
                        <i class="flaticon-025-members text-sm"></i> Karyawan
                    </a>
                    <a href="{{ route('admin.komponen-gaji.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.komponen-gaji.*') ? 'is-active' : '' }}">
                        <i class="flaticon-002-billing text-sm"></i> Komponen Gaji
                    </a>
                    <a href="{{ route('admin.aturan-absensi.edit') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.aturan-absensi.*') ? 'is-active' : '' }}">
                        <i class="flaticon-006-configuration text-sm"></i> Aturan Absensi
                    </a>
                    <a href="{{ route('admin.absensi.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.absensi.*') ? 'is-active' : '' }}">
                        <i class="flaticon-005-checklist text-sm"></i> Rekap Absensi
                    </a>
                    <a href="{{ route('admin.payroll.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.payroll.*') ? 'is-active' : '' }}">
                        <i class="flaticon-032-refresh-page text-sm"></i> Proses Payroll
                    </a>
                    <a href="{{ route('admin.slip-gaji.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.slip-gaji.*') ? 'is-active' : '' }}">
                        <i class="flaticon-013-files text-sm"></i> Slip Gaji
                    </a>

                    <div class="border-t border-white/10 my-3"></div>
                    <p class="sidebar-section-label">Laporan</p>
                    <a href="{{ route('admin.laporan.payroll') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.laporan.payroll*') ? 'is-active' : '' }}">
                        <i class="flaticon-040-stats text-sm"></i> Laporan Payroll
                    </a>
                    <a href="{{ route('admin.laporan.absensi') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.laporan.absensi*') ? 'is-active' : '' }}">
                        <i class="flaticon-041-stats text-sm"></i> Laporan Absensi
                    </a>
                @else
                    <p class="sidebar-section-label">Menu Karyawan</p>

                    <a href="{{ route('karyawan.dashboard') }}"
                        class="sidebar-nav-link {{ request()->routeIs('karyawan.dashboard') ? 'is-active' : '' }}">
                        <i class="flaticon-009-dashboard text-sm"></i> Dashboard
                    </a>
                    <a href="{{ route('karyawan.absensi.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('karyawan.absensi.*') ? 'is-active' : '' }}">
                        <i class="flaticon-005-checklist text-sm"></i> Absensi
                    </a>
                    <a href="{{ route('karyawan.slip-gaji.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('karyawan.slip-gaji.*') ? 'is-active' : '' }}">
                        <i class="flaticon-013-files text-sm"></i> Slip Gaji
                    </a>
                    <a href="{{ route('karyawan.profil.edit') }}"
                        class="sidebar-nav-link {{ request()->routeIs('karyawan.profil.*') ? 'is-active' : '' }}">
                        <i class="flaticon-030-profile text-sm"></i> Profil Saya
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-white/10 flex-shrink-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="truncate min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-body-muted capitalize truncate">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="btn btn-utility btn-sm !bg-surface-tile-1 !text-body-muted hover:!text-white">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 bg-canvas-parchment h-screen overflow-hidden">
            <header class="md:hidden flex items-center justify-between px-4 py-3 bg-white/80 backdrop-blur-xl border-b border-hairline sticky top-0 z-20">
                <div class="flex items-center gap-2.5">
                    <button id="sidebarToggle" type="button" class="text-ink hover:text-primary focus:outline-none active:scale-95 transition-all cursor-pointer p-1" aria-label="Buka menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <img src="{{ asset('logo.png') }}" class="w-5 h-5 rounded-sm object-contain" alt="Logo">
                    <span class="font-semibold text-xs tracking-tight text-ink">PT Nikel</span>
                </div>
                <div class="text-[11px] font-medium text-ink-muted-80 truncate max-w-[40%]">
                    {{ auth()->user()->name }}
                </div>
            </header>

            <main class="flex-1 p-5 md:p-8 overflow-y-auto scroll-soft bg-canvas-parchment">
                @if(session('success'))
                    <div class="alert alert-success" role="status">
                        <span aria-hidden="true">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error" role="alert">
                        <span aria-hidden="true">!</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info" role="status">
                        <span aria-hidden="true">i</span>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('sidebarClose');
            const backdrop = document.getElementById('sidebarBackdrop');
            const sidebar = document.getElementById('sidebar');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }

            if (toggleBtn && sidebar && backdrop) {
                toggleBtn.addEventListener('click', openSidebar);
            }

            if (closeBtn && sidebar && backdrop) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            if (backdrop && sidebar) {
                backdrop.addEventListener('click', closeSidebar);
            }
        });
    </script>
</body>
</html>
