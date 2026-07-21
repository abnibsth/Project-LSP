@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dashboard Saya</h1>
        <p class="page-subtitle">Selamat datang kembali, {{ auth()->user()->name }}.</p>
    </div>

    @if(!$employee)
        <div class="alert alert-warning">
            Data karyawan belum terhubung ke akun ini. Hubungi Admin/HRD.
        </div>
    @else
        <div class="ui-card ui-card-pad mb-6">
            <h2 class="text-[11px] font-semibold text-ink-muted-48 uppercase tracking-widest mb-5">
                Absensi Hari Ini — {{ now()->translatedFormat('d F Y') }}
            </h2>

            @if(!$absensiHariIni)
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon"><i class="flaticon-003-calendar"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="typography-body-strong text-ink">Belum Check-In</p>
                        <p class="text-sm text-ink-muted-48 mt-0.5">Lakukan check-in untuk mencatat jam masuk kerja hari ini.</p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkin') }}" class="sm:ml-auto">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full sm:w-auto">Check-In Sekarang</button>
                    </form>
                </div>
            @elseif(!$absensiHariIni->waktu_checkout)
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon"><i class="flaticon-005-checklist"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="typography-body-strong text-ink">Sudah Check-In</p>
                        <p class="text-sm text-ink-muted-48 mt-0.5">
                            Masuk pukul {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                            —
                            <span class="font-semibold {{ $absensiHariIni->status === 'telat' ? 'text-amber-600' : 'text-primary' }}">
                                {{ ucfirst($absensiHariIni->status) }}
                                @if($absensiHariIni->status === 'telat')
                                    ({{ $absensiHariIni->menit_terlambat }} menit)
                                @endif
                            </span>
                        </p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkout') }}" class="sm:ml-auto">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full sm:w-auto">Check-Out Pulang</button>
                    </form>
                </div>
            @else
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon"><i class="flaticon-015-home"></i></div>
                    <div>
                        <p class="typography-body-strong text-ink">Kehadiran Selesai</p>
                        <p class="text-sm text-ink-muted-48 mt-0.5">
                            Masuk {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                            — Pulang {{ $absensiHariIni->waktu_checkout->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div class="ui-card ui-card-pad">
                <h2 class="text-[11px] font-semibold text-ink-muted-48 uppercase tracking-widest mb-4">
                    Kehadiran Bulan Ini
                </h2>
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-4 bg-canvas-parchment border border-hairline rounded-lg">
                        <p class="text-2xl font-semibold text-primary tracking-tight">{{ $rekapAbsensi['hadir'] }}</p>
                        <p class="text-[10px] font-semibold text-ink-muted-48 mt-1 uppercase tracking-wider">Hadir</p>
                    </div>
                    <div class="text-center p-4 bg-canvas-parchment border border-hairline rounded-lg">
                        <p class="text-2xl font-semibold text-amber-600 tracking-tight">{{ $rekapAbsensi['telat'] }}</p>
                        <p class="text-[10px] font-semibold text-ink-muted-48 mt-1 uppercase tracking-wider">Telat</p>
                    </div>
                    <div class="text-center p-4 bg-canvas-parchment border border-hairline rounded-lg">
                        <p class="text-2xl font-semibold text-red-600 tracking-tight">{{ $rekapAbsensi['alpha'] }}</p>
                        <p class="text-[10px] font-semibold text-ink-muted-48 mt-1 uppercase tracking-wider">Alpha</p>
                    </div>
                </div>
            </div>

            <div class="ui-card ui-card-pad">
                <h2 class="text-[11px] font-semibold text-ink-muted-48 uppercase tracking-widest mb-4">
                    Gaji Terakhir
                </h2>
                @if($slipTerakhir)
                    <div class="flex flex-col justify-between min-h-[88px]">
                        <div>
                            <p class="text-3xl font-semibold text-ink tracking-tight">{{ $slipTerakhir->gaji_bersih_format }}</p>
                            <p class="text-sm text-ink-muted-48 mt-1">{{ $slipTerakhir->payrollPeriod->label }}</p>
                        </div>
                        <a href="{{ route('karyawan.slip-gaji.show', $slipTerakhir) }}" class="text-sm text-primary font-medium mt-4 inline-flex items-center gap-1">
                            Lihat detail slip gaji <span aria-hidden="true">→</span>
                        </a>
                    </div>
                @else
                    <div class="flex items-center justify-center min-h-[88px]">
                        <p class="text-ink-muted-48 text-sm">Belum ada slip gaji yang tersedia.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
