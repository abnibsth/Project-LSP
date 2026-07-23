@extends('layouts.app')

@section('title', 'Slip Gaji Saya')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Daftar slip gaji final milik karyawan yang login.
        Tiap kartu: periode, gaji bersih, aksi Detail + PDF.

        Catatan desain:
        - Tanpa emoji / hijau “success” berlebihan
        - Tombol pakai design system (btn-ghost / btn-utility)
    --}}

    <div class="page-header">
        <h1 class="page-title">Slip Gaji Saya</h1>
        <p class="page-subtitle">Riwayat slip gaji yang sudah diterbitkan HRD.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($payslips as $payslip)
            <div class="ui-card ui-card-pad flex flex-col">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0">
                        <h3 class="section-title truncate">{{ $payslip->payrollPeriod->label }}</h3>
                        <p class="text-xs text-ink-muted-48 mt-0.5">Take-home pay</p>
                    </div>
                    <span class="badge badge-muted shrink-0">Final</span>
                </div>

                <p class="text-[1.65rem] font-semibold text-ink tracking-tight tabular-nums">
                    {{ $payslip->gaji_bersih_format }}
                </p>

                <div class="mt-auto pt-4 flex gap-2">
                    <a href="{{ route('karyawan.slip-gaji.show', $payslip) }}"
                        class="btn btn-ghost flex-1 !rounded-md">
                        Detail
                    </a>
                    <a href="{{ route('karyawan.slip-gaji.download', $payslip) }}"
                        class="btn btn-utility flex-1 js-file-download"
                        data-download="true"
                        download>
                        <i data-lucide="download" class="ui-icon ui-icon-sm" aria-hidden="true"></i>
                        PDF
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i data-lucide="file-text" class="ui-icon" aria-hidden="true"></i>
                    </div>
                    <h3 class="empty-state-title">Belum ada slip gaji</h3>
                    <p class="empty-state-text">
                        Slip muncul di sini setelah HRD memproses dan finalisasi payroll.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    @if($payslips->hasPages())
        <div class="mt-5">{{ $payslips->links() }}</div>
    @endif
@endsection
