@extends('layouts.app')

@section('title', 'Slip Gaji')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 page-header">
        <div>
            <h1 class="page-title">Slip Gaji Karyawan</h1>
            <p class="page-subtitle">Daftar slip gaji dari semua periode yang sudah difinalisasi.</p>
        </div>
        <a href="{{ route('admin.payroll.index') }}" class="btn btn-primary self-start">Ke Proses Payroll</a>
    </div>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Departemen</th>
                    <th>Periode</th>
                    <th class="!text-right">Gaji Pokok</th>
                    <th class="!text-right">Total Tunjangan</th>
                    <th class="!text-right">Total Potongan</th>
                    <th class="!text-right">Gaji Bersih</th>
                    <th class="!text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payslips as $payslip)
                    <tr>
                        <td>
                            <p class="font-medium text-ink">{{ $payslip->employee->nama }}</p>
                            <p class="text-xs text-ink-muted-48 font-mono">{{ $payslip->employee->nik }}</p>
                        </td>
                        <td class="text-ink-muted-80">{{ $payslip->employee->departemen }}</td>
                        <td>
                            <span class="badge badge-primary">{{ $payslip->payrollPeriod->label }}</span>
                        </td>
                        <td class="text-right text-ink-muted-80">
                            Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}
                        </td>
                        <td class="text-right text-green-600 font-medium">
                            + Rp {{ number_format($payslip->total_tunjangan, 0, ',', '.') }}
                        </td>
                        <td class="text-right text-red-500 font-medium">
                            − Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}
                        </td>
                        <td class="text-right font-semibold text-ink">
                            Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="flex gap-1.5 justify-center items-center">
                                <a href="{{ route('admin.slip-gaji.show', $payslip) }}" class="btn btn-action-soft">Detail</a>
                                <a href="{{ route('admin.slip-gaji.download', $payslip) }}" class="btn btn-action-soft !bg-green-50 !text-green-700">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="!text-center !py-12 text-ink-muted-48">
                            <p class="font-medium text-ink mb-1">Belum ada slip gaji</p>
                            <p class="text-xs">
                                Jalankan proses payroll dan finalisasi terlebih dahulu.
                                <a href="{{ route('admin.payroll.index') }}" class="text-primary ml-1">Ke Payroll →</a>
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($payslips->hasPages())
            <div class="px-6 py-4 border-t border-divider-soft">
                {{ $payslips->links() }}
            </div>
        @endif
    </div>
@endsection
