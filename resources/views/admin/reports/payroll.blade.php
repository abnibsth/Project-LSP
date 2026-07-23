@extends('layouts.app')

@section('title', 'Laporan Payroll')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Menampilkan laporan rekap payroll per bulan:
        - Total pengeluaran gaji (bruto, potongan, bersih)
        - Rincian per karyawan
        Admin bisa filter per bulan/tahun.
    --}}

    <div class="page-header">
        <h1 class="page-title">Laporan Payroll</h1>
        <p class="page-subtitle">Rekap pengeluaran gaji bulanan.</p>
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

    @if($periode)
        {{-- Kartu Statistik Total — stack di HP --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
            <div class="ui-card ui-card-pad text-center">
                <p class="text-xs text-ink-muted-48 mb-1">Total Karyawan</p>
                <p class="text-2xl font-semibold text-ink tabular-nums">{{ $periode->payslips->count() }}</p>
            </div>
            <div class="ui-card ui-card-pad text-center">
                <p class="text-xs text-ink-muted-48 mb-1">Total Gaji Bruto</p>
                <p class="text-lg sm:text-xl font-semibold text-ink tabular-nums break-words">
                    Rp {{ number_format($totalGajiBruto, 0, ',', '.') }}
                </p>
            </div>
            <div class="ui-card ui-card-pad text-center">
                <p class="text-xs text-ink-muted-48 mb-1">Total Gaji Bersih (THP)</p>
                <p class="text-lg sm:text-xl font-semibold text-emerald-700 tabular-nums break-words">
                    Rp {{ number_format($totalGajiBersih, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Tabel Rincian Per Karyawan — scroll di HP --}}
        <div class="ui-table-wrap">
            <div class="ui-card-header flex flex-wrap items-center justify-between gap-2">
                <h2 class="section-title">
                    Rincian Payroll — {{ $periode->label }}
                </h2>
                <span class="badge {{ $periode->status === 'final' ? 'badge-success' : 'badge-warning' }}">
                    {{ $periode->status === 'final' ? 'Final' : 'Draft' }}
                </span>
            </div>
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Departemen</th>
                        <th class="!text-right">Gaji Bruto</th>
                        <th class="!text-right">Total Potongan</th>
                        <th class="!text-right">Gaji Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($periode->payslips as $slip)
                        <tr>
                            <td>
                                <p class="font-medium text-ink">{{ $slip->employee->nama }}</p>
                                <p class="text-xs text-ink-muted-48 font-mono">{{ $slip->employee->nik }}</p>
                            </td>
                            <td class="text-ink-muted-80">{{ $slip->employee->departemen }}</td>
                            <td class="text-right tabular-nums">Rp {{ number_format($slip->gaji_bruto, 0, ',', '.') }}</td>
                            <td class="text-right text-red-500 tabular-nums">- Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}</td>
                            <td class="text-right font-semibold text-emerald-700 tabular-nums">Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    {{-- Baris Total --}}
                    <tr class="bg-canvas-parchment font-semibold">
                        <td colspan="2">TOTAL</td>
                        <td class="text-right tabular-nums">Rp {{ number_format($totalGajiBruto, 0, ',', '.') }}</td>
                        <td class="text-right text-red-500 tabular-nums">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                        <td class="text-right text-emerald-700 tabular-nums">Rp {{ number_format($totalGajiBersih, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        {{-- Jika belum ada periode payroll untuk bulan ini --}}
        <div class="empty-state">
            <div class="empty-state-icon">
                <i data-lucide="chart-column" class="ui-icon" aria-hidden="true"></i>
            </div>
            <h3 class="empty-state-title">Belum ada data payroll</h3>
            <p class="empty-state-text">
                Belum ada data untuk
                <strong>{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</strong>.
            </p>
            <a href="{{ route('admin.payroll.index') }}" class="btn btn-primary">
                Ke Proses Payroll
            </a>
        </div>
    @endif
@endsection
