@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dashboard Saya</h1>
        <p class="page-subtitle">Selamat datang kembali, {{ auth()->user()->name }}.</p>
    </div>

    @if(!$employee)
        <div class="alert alert-warning">
            <span class="alert-icon" aria-hidden="true">!</span>
            <span>Data karyawan belum terhubung ke akun ini. Hubungi Admin/HRD.</span>
        </div>
    @else
        <div class="ui-card ui-card-pad mb-6">
            <p class="section-label mb-4">
                Absensi Hari Ini — {{ now()->translatedFormat('d F Y') }}
            </p>

            @if(!$absensiHariIni)
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon"><i data-lucide="calendar-clock" class="ui-icon" aria-hidden="true"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="section-title">Belum check-in</p>
                        <p class="text-sm text-ink-muted-48 mt-0.5">Catat jam masuk kerja untuk hari ini.</p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkin') }}" class="sm:ml-auto">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full sm:w-auto">Check-In</button>
                    </form>
                </div>
            @elseif(!$absensiHariIni->waktu_checkout)
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon is-success"><i data-lucide="clipboard-check" class="ui-icon" aria-hidden="true"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="section-title">Sudah check-in</p>
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
                        <button type="submit" class="btn btn-primary w-full sm:w-auto">Check-Out</button>
                    </form>
                </div>
            @else
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon"><i data-lucide="circle-check" class="ui-icon" aria-hidden="true"></i></div>
                    <div>
                        <p class="section-title">Kehadiran selesai</p>
                        <p class="text-sm text-ink-muted-48 mt-0.5">
                            Masuk {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                            — Pulang {{ $absensiHariIni->waktu_checkout->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="ui-card ui-card-pad">
                <p class="section-label mb-4">Kehadiran Bulan Ini</p>
                <div class="grid grid-cols-3 gap-2.5">
                    <div class="metric-tile">
                        <p class="metric-tile-value text-primary tabular-nums">{{ $rekapAbsensi['hadir'] }}</p>
                        <p class="metric-tile-label">Hadir</p>
                    </div>
                    <div class="metric-tile">
                        <p class="metric-tile-value text-amber-600 tabular-nums">{{ $rekapAbsensi['telat'] }}</p>
                        <p class="metric-tile-label">Telat</p>
                    </div>
                    <div class="metric-tile">
                        <p class="metric-tile-value text-red-600 tabular-nums">{{ $rekapAbsensi['alpha'] }}</p>
                        <p class="metric-tile-label">Alpha</p>
                    </div>
                </div>
            </div>

            <div class="ui-card ui-card-pad">
                <p class="section-label mb-4">Gaji Terakhir</p>
                @if($slipTerakhir)
                    <div class="flex flex-col justify-between min-h-[5.5rem]">
                        <div>
                            <p class="text-[1.75rem] font-semibold text-ink tracking-tight tabular-nums">{{ $slipTerakhir->gaji_bersih_format }}</p>
                            <p class="text-sm text-ink-muted-48 mt-1">{{ $slipTerakhir->payrollPeriod->label }}</p>
                        </div>
                        <a href="{{ route('karyawan.slip-gaji.show', $slipTerakhir) }}" class="text-sm text-primary font-medium mt-4 inline-flex items-center gap-1 hover:underline">
                            Lihat detail slip
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                @else
                    <div class="flex items-center justify-center min-h-[5.5rem]">
                        <p class="text-ink-muted-48 text-sm">Belum ada slip gaji.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
