@extends('layouts.app')

@section('title', 'Absensi Saya')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Absensi Saya</h1>
        <p class="page-subtitle">Lakukan check-in dan check-out harian di sini.</p>
    </div>

    <div class="ui-card ui-card-pad mb-6 text-center">
        <p class="text-sm font-medium text-ink-muted-48">{{ now()->translatedFormat('l, d F Y') }}</p>
        <p class="text-5xl font-semibold text-ink mt-2 tracking-tight tabular-nums" id="jam-sekarang">{{ now()->format('H:i:s') }}</p>
        <p class="mt-3 text-sm text-ink-muted-48">
            Jam Kerja: {{ $rule->jam_masuk }} — {{ $rule->jam_keluar }}
            (Toleransi {{ $rule->toleransi_menit }} menit)
        </p>

        <div class="mt-6 flex flex-wrap gap-3 justify-center items-center">
            @if(!$absensiHariIni)
                <form method="POST" action="{{ route('karyawan.absensi.checkin') }}" id="form-checkin">
                    @csrf
                    <button type="submit" id="btn-checkin" class="btn btn-primary !px-8 !py-3 !text-base">
                        Check-In Sekarang
                    </button>
                </form>
            @elseif(!$absensiHariIni->waktu_checkout)
                <div class="badge badge-success !px-4 !py-2 !text-sm">
                    Check-in: {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                    · {{ ucfirst($absensiHariIni->status) }}
                </div>
                <form method="POST" action="{{ route('karyawan.absensi.checkout') }}" id="form-checkout">
                    @csrf
                    <button type="submit" id="btn-checkout" class="btn btn-utility !px-8 !py-3 !text-base">
                        Check-Out
                    </button>
                </form>
            @else
                <div class="badge badge-primary !px-5 !py-2.5 !text-sm">
                    Selesai — Masuk {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                    · Pulang {{ $absensiHariIni->waktu_checkout->format('H:i') }}
                </div>
            @endif
        </div>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-4 items-center">
        <select name="bulan" class="form-select w-auto">
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
        <select name="tahun" class="form-select w-auto">
            @foreach(range(date('Y'), date('Y') - 3) as $y)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>

    <div class="ui-table-wrap">
        <div class="px-5 py-4 border-b border-divider-soft">
            <h2 class="font-semibold text-ink">Rekap Kehadiran</h2>
        </div>
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapAbsensi as $absensi)
                    <tr>
                        <td class="font-medium">{{ $absensi->tanggal->translatedFormat('d F Y') }}</td>
                        <td>{{ $absensi->waktu_checkin?->format('H:i') ?? '-' }}</td>
                        <td>{{ $absensi->waktu_checkout?->format('H:i') ?? '-' }}</td>
                        <td>
                            <span class="badge
                                @if($absensi->status === 'hadir') badge-success
                                @elseif($absensi->status === 'telat') badge-warning
                                @else badge-danger
                                @endif">
                                {{ ucfirst($absensi->status) }}
                                @if($absensi->status === 'telat')
                                    ({{ $absensi->menit_terlambat }} menit)
                                @endif
                            </span>
                        </td>
                        <td class="text-ink-muted-48">
                            @if($absensi->is_koreksi)
                                <span class="text-amber-600">Dikoreksi admin</span>
                            @endif
                            {{ $absensi->keterangan ?? '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="!text-center !py-8 text-ink-muted-48">
                            Tidak ada data absensi untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setInterval(() => {
                const clock = document.getElementById('jam-sekarang');
                if (clock) {
                    clock.textContent = new Date().toTimeString().split(' ')[0];
                }
            }, 1000);
        });
    </script>
@endsection
