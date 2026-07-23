@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Menampilkan laporan rekap kehadiran semua karyawan dalam satu bulan.
        withCount() di controller menghitung hadir/telat/alpha per karyawan
        tanpa harus load semua data absensi (lebih efisien di database).
    --}}

    <div class="page-header">
        <h1 class="page-title">Laporan Absensi</h1>
        <p class="page-subtitle">Rekap kehadiran karyawan per bulan.</p>
    </div>

    {{-- Filter Bulan & Tahun --}}
    <form method="GET" class="filter-bar">
        <div class="w-full sm:w-auto">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-select">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="w-full sm:w-auto">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-select">
                @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-ghost">Tampilkan</button>
    </form>

    {{-- Tabel Rekap Absensi Per Karyawan — scroll di HP --}}
    <div class="ui-table-wrap">
        <div class="ui-card-header">
            <h2 class="section-title">
                Rekap Kehadiran — {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
            </h2>
        </div>
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Departemen</th>
                    {{-- Kolom ini diisi dari withCount() di controller --}}
                    <th class="!text-center">Hadir</th>
                    <th class="!text-center">Telat</th>
                    <th class="!text-center">Alpha</th>
                    <th class="!text-center">Total Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                    <tr>
                        <td>
                            <p class="font-medium text-ink">{{ $emp->nama }}</p>
                            <p class="text-xs text-ink-muted-48 font-mono">{{ $emp->nik }}</p>
                        </td>
                        <td class="text-ink-muted-80">{{ $emp->departemen }}</td>
                        <td class="text-center">
                            {{-- total_hadir adalah hasil withCount() di controller --}}
                            <span class="font-medium text-emerald-700 tabular-nums">{{ $emp->total_hadir }}</span>
                        </td>
                        <td class="text-center">
                            <span class="font-medium text-amber-600 tabular-nums">{{ $emp->total_telat }}</span>
                        </td>
                        <td class="text-center">
                            <span class="font-medium text-red-600 tabular-nums">{{ $emp->total_alpha }}</span>
                        </td>
                        <td class="text-center">
                            {{-- Total kehadiran = hadir + telat (keduanya masuk kerja, hanya beda waktu) --}}
                            <span class="font-semibold text-ink tabular-nums">
                                {{ $emp->total_hadir + $emp->total_telat }} hari
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">
                            Tidak ada data karyawan aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
