<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Penggajian') — PT Nikel Indonesia</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas-parchment text-ink antialiased font-sans">

    <div id="nprogress-bar" aria-hidden="true"></div>

    <div class="flex h-screen overflow-hidden relative">
        <div id="sidebarBackdrop" class="fixed inset-0 bg-surface-black/40 z-30 hidden md:hidden transition-opacity duration-300"></div>

        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-[15.5rem] bg-[#161617] flex-shrink-0 transform -translate-x-full md:translate-x-0 md:relative md:flex md:flex-col transition-transform duration-300 ease-in-out h-screen border-r border-white/5">
            <div class="px-4 py-4 border-b border-white/8 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <img src="{{ asset('logo.png') }}" class="w-8 h-8 rounded-[7px] object-contain bg-white p-0.5 shrink-0" alt="Logo">
                    <div class="min-w-0">
                        <span class="font-semibold text-[13px] tracking-tight block text-white truncate">PT Nikel</span>
                        <span class="text-[10px] text-body-muted block tracking-wide">Penggajian</span>
                    </div>
                </div>
                <button id="sidebarClose" type="button" class="md:hidden text-body-muted hover:text-white cursor-pointer p-1 rounded-sm active:scale-95" aria-label="Tutup menu">
                    <i data-lucide="x" class="ui-icon" aria-hidden="true"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto scroll-soft p-3 space-y-0.5">
                @if(auth()->user()->isAdmin())
                    <p class="sidebar-section-label">Menu Admin</p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <i data-lucide="layout-dashboard" class="ui-icon" aria-hidden="true"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.karyawan.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.karyawan.*') ? 'is-active' : '' }}">
                        <i data-lucide="users" class="ui-icon" aria-hidden="true"></i> Karyawan
                    </a>
                    <a href="{{ route('admin.komponen-gaji.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.komponen-gaji.*') ? 'is-active' : '' }}">
                        <i data-lucide="wallet" class="ui-icon" aria-hidden="true"></i> Komponen Gaji
                    </a>
                    <a href="{{ route('admin.aturan-absensi.edit') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.aturan-absensi.*') ? 'is-active' : '' }}">
                        <i data-lucide="settings-2" class="ui-icon" aria-hidden="true"></i> Aturan Absensi
                    </a>
                    <a href="{{ route('admin.absensi.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.absensi.*') ? 'is-active' : '' }}">
                        <i data-lucide="clipboard-check" class="ui-icon" aria-hidden="true"></i> Rekap Absensi
                    </a>
                    <a href="{{ route('admin.payroll.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.payroll.*') ? 'is-active' : '' }}">
                        <i data-lucide="calculator" class="ui-icon" aria-hidden="true"></i> Proses Payroll
                    </a>
                    <a href="{{ route('admin.slip-gaji.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.slip-gaji.*') ? 'is-active' : '' }}">
                        <i data-lucide="file-text" class="ui-icon" aria-hidden="true"></i> Slip Gaji
                    </a>

                    <div class="border-t border-white/8 my-3 mx-1"></div>
                    <p class="sidebar-section-label">Laporan</p>
                    <a href="{{ route('admin.laporan.payroll') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.laporan.payroll*') ? 'is-active' : '' }}">
                        <i data-lucide="chart-column" class="ui-icon" aria-hidden="true"></i> Laporan Payroll
                    </a>
                    <a href="{{ route('admin.laporan.absensi') }}"
                        class="sidebar-nav-link {{ request()->routeIs('admin.laporan.absensi*') ? 'is-active' : '' }}">
                        <i data-lucide="chart-pie" class="ui-icon" aria-hidden="true"></i> Laporan Absensi
                    </a>
                @else
                    <p class="sidebar-section-label">Menu Karyawan</p>

                    <a href="{{ route('karyawan.dashboard') }}"
                        class="sidebar-nav-link {{ request()->routeIs('karyawan.dashboard') ? 'is-active' : '' }}">
                        <i data-lucide="layout-dashboard" class="ui-icon" aria-hidden="true"></i> Dashboard
                    </a>
                    <a href="{{ route('karyawan.absensi.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('karyawan.absensi.*') ? 'is-active' : '' }}">
                        <i data-lucide="clipboard-check" class="ui-icon" aria-hidden="true"></i> Absensi
                    </a>
                    <a href="{{ route('karyawan.slip-gaji.index') }}"
                        class="sidebar-nav-link {{ request()->routeIs('karyawan.slip-gaji.*') ? 'is-active' : '' }}">
                        <i data-lucide="file-text" class="ui-icon" aria-hidden="true"></i> Slip Gaji
                    </a>
                    <a href="{{ route('karyawan.profil.edit') }}"
                        class="sidebar-nav-link {{ request()->routeIs('karyawan.profil.*') ? 'is-active' : '' }}">
                        <i data-lucide="user-round" class="ui-icon" aria-hidden="true"></i> Profil Saya
                    </a>
                @endif
            </nav>

            <div class="p-3 border-t border-white/8 flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="user-avatar" aria-hidden="true">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate min-w-0 flex-1">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-body-muted capitalize truncate">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="btn btn-sm !bg-white/8 !text-body-muted hover:!text-white hover:!bg-white/12 !rounded-md !px-2.5 inline-flex items-center gap-1.5" title="Keluar">
                            <i data-lucide="log-out" class="ui-icon ui-icon-sm" aria-hidden="true"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 bg-canvas-parchment h-screen overflow-hidden">
            <header class="md:hidden flex items-center justify-between px-4 py-3 bg-white/85 backdrop-blur-xl border-b border-hairline sticky top-0 z-20">
                <div class="flex items-center gap-2.5">
                    <button id="sidebarToggle" type="button" class="text-ink hover:text-primary focus:outline-none active:scale-95 transition-all cursor-pointer p-1" aria-label="Buka menu">
                        <i data-lucide="menu" class="ui-icon ui-icon-md" aria-hidden="true"></i>
                    </button>
                    <img src="{{ asset('logo.png') }}" class="w-5 h-5 rounded-sm object-contain" alt="Logo">
                    <span class="font-semibold text-xs tracking-tight text-ink">PT Nikel</span>
                </div>
                <div class="text-[11px] font-medium text-ink-muted-80 truncate max-w-[40%]">
                    {{ auth()->user()->name }}
                </div>
            </header>

            <main id="page-content" class="flex-1 p-4 sm:p-5 md:p-8 overflow-y-auto scroll-soft bg-canvas-parchment">
                @if(session('success'))
                    <div class="alert alert-success" role="status">
                        <span class="alert-icon" aria-hidden="true">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error" role="alert">
                        <span class="alert-icon" aria-hidden="true">!</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info" role="status">
                        <span class="alert-icon" aria-hidden="true">i</span>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Lucide: satu family outline, stroke konsisten (bukan Flaticon acak) --}}
    <script
        src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"
        onload="if (window.lucide) { window.lucide.createIcons({ attrs: { 'stroke-width': 1.75 } }); }"
    ></script>
</body>
</html>
