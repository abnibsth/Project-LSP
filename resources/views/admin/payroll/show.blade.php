@extends('layouts.app')

@section('title', 'Detail Payroll ' . $payrollPeriod->label)

@section('content')
    {{--
        Header + aksi payroll.
        Proses  = hitung/ulang hitung gaji → tombol primary (aksi utama)
        Finalisasi = kunci data permanen → tombol utility gelap (tegas, bukan hijau "rayakan")
        Tanpa emoji / ungu / hijau flat biar tidak terasa AI-slop.
    --}}
    <div class="page-toolbar">
        <div class="min-w-0">
            <a href="{{ route('admin.payroll.index') }}" class="back-link">← Kembali ke daftar payroll</a>
            <div class="flex flex-wrap items-center gap-2.5 mt-1">
                <h1 class="page-title">Payroll: {{ $payrollPeriod->label }}</h1>
                <span class="badge {{ $payrollPeriod->status === 'final' ? 'badge-success' : 'badge-warning' }}">
                    {{ $payrollPeriod->status === 'final' ? 'Final' : 'Draft' }}
                </span>
            </div>
        </div>

        @if(!$payrollPeriod->isFinal())
            <div class="flex flex-wrap items-center gap-2 self-start">
                <form method="POST" action="{{ route('admin.payroll.proses', $payrollPeriod) }}"
                    onsubmit="return confirm('Proses/hitung ulang payroll {{ $payrollPeriod->label }}?')" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Proses Payroll
                    </button>
                </form>

                @if($payrollPeriod->payslips->count() > 0)
                    <form method="POST" action="{{ route('admin.payroll.finalisasi', $payrollPeriod) }}"
                        onsubmit="return confirm('Finalisasi payroll {{ $payrollPeriod->label }}? Data akan dikunci permanen!')" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-utility">
                            Finalisasi
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- Statistik Total — stack di HP, 3 kolom di tablet+ --}}
    @if($payrollPeriod->payslips->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
            <div class="ui-card ui-card-pad text-center">
                <p class="text-xs text-ink-muted-48">Total Karyawan</p>
                <p class="text-2xl font-semibold text-ink mt-1 tabular-nums">{{ $payrollPeriod->payslips->count() }}</p>
            </div>
            <div class="ui-card ui-card-pad text-center">
                <p class="text-xs text-ink-muted-48">Total Gaji Bruto</p>
                <p class="text-lg sm:text-xl font-semibold text-ink mt-1 tabular-nums break-words">
                    Rp {{ number_format($payrollPeriod->payslips->sum('gaji_bruto'), 0, ',', '.') }}
                </p>
            </div>
            <div class="ui-card ui-card-pad text-center">
                <p class="text-xs text-ink-muted-48">Total Gaji Bersih</p>
                <p class="text-lg sm:text-xl font-semibold text-emerald-700 mt-1 tabular-nums break-words">
                    Rp {{ number_format($payrollPeriod->payslips->sum('gaji_bersih'), 0, ',', '.') }}
                </p>
            </div>
        </div>
    @endif

    {{-- Tabel Slip Gaji Per Karyawan — scroll horizontal di HP --}}
    <div class="ui-table-wrap">
        <div class="ui-card-header">
            <h2 class="section-title">Daftar Slip Gaji Karyawan</h2>
        </div>
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th class="!text-right">Gaji Bruto</th>
                    <th class="!text-right">Total Potongan</th>
                    <th class="!text-right">Gaji Bersih</th>
                    <th class="!text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrollPeriod->payslips as $payslip)
                    <tr>
                        <td>
                            <p class="font-medium text-ink">{{ $payslip->employee->nama }}</p>
                            <p class="text-xs text-ink-muted-48">{{ $payslip->employee->jabatan }} — {{ $payslip->employee->departemen }}</p>
                        </td>
                        <td class="text-right tabular-nums">Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}</td>
                        <td class="text-right text-red-600 font-medium tabular-nums">Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}</td>
                        <td class="text-right font-semibold text-emerald-700 tabular-nums">Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}</td>
                        <td>
                            <div class="action-group justify-center">
                                <a href="{{ route('admin.slip-gaji.show', $payslip) }}" class="btn btn-action-soft">Detail</a>
                                <a href="{{ route('admin.slip-gaji.download', $payslip) }}"
                                    class="btn btn-muted-soft js-file-download"
                                    data-download="true"
                                    download>
                                    PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-cell">
                            Belum ada data. Klik "Proses Payroll" untuk menghitung gaji karyawan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
