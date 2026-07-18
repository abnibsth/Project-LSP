@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-ink tracking-tight">Dashboard Saya</h1>
        <p class="text-ink-muted-48 text-xs mt-0.5">Selamat datang kembali, {{ auth()->user()->name }}.</p>
    </div>

    @if(!$employee)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-sm px-4 py-3 text-xs">
            Data karyawan belum terhubung ke akun ini. Hubungi Admin/HRD.
        </div>
    @else
        {{-- Status Absensi Hari Ini (Apple Style - flat, rounded-lg, border-hairline) --}}
        <div class="bg-white rounded-lg border border-hairline p-6 shadow-none mb-6">
            <h2 class="text-xs font-bold text-ink-muted-48 uppercase tracking-widest mb-4">
                <i class="flaticon-003-calendar text-primary mr-1.5"></i> Absensi Hari Ini — {{ now()->translatedFormat('d F Y') }}
            </h2>

            @if(!$absensiHariIni)
                {{-- Belum check-in --}}
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-canvas-parchment border border-hairline rounded-sm flex items-center justify-center text-primary text-base">
                        <i class="flaticon-003-calendar"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink">Belum Check-In Kehadiran</p>
                        <p class="text-[10px] text-ink-muted-48 mt-0.5">Silakan lakukan check-in untuk mencatat jam masuk kerja Anda hari ini.</p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkin') }}" class="ml-auto">
                        @csrf
                        <button type="submit"
                            class="bg-primary hover:bg-primary-focus text-white font-medium px-5 py-2 rounded-pill text-xs transition-all active:scale-95">
                            Check-In Sekarang
                        </button>
                    </form>
                </div>
            @elseif(!$absensiHariIni->waktu_checkout)
                {{-- Sudah check-in, belum check-out --}}
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-canvas-parchment border border-hairline rounded-sm flex items-center justify-center text-primary text-base">
                        <i class="flaticon-005-checklist"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink">Sudah Check-In Kehadiran</p>
                        <p class="text-[10px] text-ink-muted-48 mt-0.5">
                            Check-in masuk pukul {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                            — Status:
                            <span class="font-bold {{ $absensiHariIni->status === 'telat' ? 'text-amber-600' : 'text-primary' }}">
                                {{ ucfirst($absensiHariIni->status) }}
                                @if($absensiHariIni->status === 'telat')
                                    ({{ $absensiHariIni->menit_terlambat }} menit)
                                @endif
                            </span>
                        </p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkout') }}" class="ml-auto">
                        @csrf
                        <button type="submit"
                            class="bg-primary hover:bg-primary-focus text-white font-medium px-5 py-2 rounded-pill text-xs transition-all active:scale-95">
                            Check-Out Pulang
                        </button>
                    </form>
                </div>
            @else
                {{-- Sudah check-out --}}
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-canvas-parchment border border-hairline rounded-sm flex items-center justify-center text-primary text-base">
                        <i class="flaticon-015-home"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink">Kehadiran Selesai Hari Ini</p>
                        <p class="text-[10px] text-ink-muted-48 mt-0.5">
                            Masuk: {{ $absensiHariIni->waktu_checkin->format('H:i') }} —
                            Pulang: {{ $absensiHariIni->waktu_checkout->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Ringkasan Kehadiran Bulan Ini --}}
            <div class="bg-white rounded-lg border border-hairline p-6 shadow-none">
                <h2 class="text-xs font-bold text-ink-muted-48 uppercase tracking-widest mb-4">
                    <i class="flaticon-040-stats text-primary mr-1.5"></i> Kehadiran Bulan Ini
                </h2>
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3.5 bg-canvas-parchment border border-hairline rounded-lg">
                        <p class="text-xl font-bold text-primary">{{ $rekapAbsensi['hadir'] }}</p>
                        <p class="text-[10px] font-semibold text-ink-muted-48 mt-1 uppercase tracking-wider">Hadir</p>
                    </div>
                    <div class="text-center p-3.5 bg-canvas-parchment border border-hairline rounded-lg">
                        <p class="text-xl font-bold text-amber-600">{{ $rekapAbsensi['telat'] }}</p>
                        <p class="text-[10px] font-semibold text-ink-muted-48 mt-1 uppercase tracking-wider">Telat</p>
                    </div>
                    <div class="text-center p-3.5 bg-canvas-parchment border border-hairline rounded-lg">
                        <p class="text-xl font-bold text-red-600">{{ $rekapAbsensi['alpha'] }}</p>
                        <p class="text-[10px] font-semibold text-ink-muted-48 mt-1 uppercase tracking-wider">Alpha</p>
                    </div>
                </div>
            </div>

            {{-- Gaji Terakhir --}}
            <div class="bg-white rounded-lg border border-hairline p-6 shadow-none">
                <h2 class="text-xs font-bold text-ink-muted-48 uppercase tracking-widest mb-4">
                    <i class="flaticon-002-billing text-primary mr-1.5"></i> Gaji Terakhir
                </h2>
                @if($slipTerakhir)
                    <div class="flex flex-col justify-between h-[82px]">
                        <div>
                            <p class="text-2xl font-bold text-ink tracking-tight">{{ $slipTerakhir->gaji_bersih_format }}</p>
                            <p class="text-[10px] text-ink-muted-48 mt-0.5">{{ $slipTerakhir->payrollPeriod->label }}</p>
                        </div>
                        <div>
                            <a href="{{ route('karyawan.slip-gaji.show', $slipTerakhir) }}"
                                class="text-xs text-primary hover:underline font-semibold flex items-center gap-1">
                                Lihat Detail Slip Gaji <span>→</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-[82px]">
                        <p class="text-ink-muted-48 text-xs">Belum ada slip gaji yang tersedia.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
