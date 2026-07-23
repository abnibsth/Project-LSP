@extends('layouts.app')

@section('title', 'Detail Slip Gaji ' . $payslip->payrollPeriod->label)

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Menampilkan rincian slip gaji final milik karyawan yang login.
        Layout full-width (bukan max-w sempit) biar tidak “nempel kiri”.

        Struktur:
        1. Header + tombol Download PDF
        2. Data karyawan (identitas)
        3. Grid 2 kolom: Pendapatan | Potongan
        4. Banner take-home pay (gaji bersih)

        Catatan:
        - components di-load di controller (eager load)
        - detail_json = snapshot rekap absensi saat payroll diproses
    --}}

    {{-- =====================================================
         BAGIAN 1: Header halaman
         page-toolbar = judul kiri, aksi kanan (seimbang)
         ===================================================== --}}
    <div class="page-toolbar">
        <div class="min-w-0">
            <a href="{{ route('karyawan.slip-gaji.index') }}" class="back-link">
                ← Kembali ke daftar slip
            </a>
            <h1 class="page-title">Slip Gaji — {{ $payslip->payrollPeriod->label }}</h1>
            <p class="page-subtitle">
                Rincian penghasilan, potongan, dan gaji bersih periode ini.
            </p>
        </div>
        {{-- PDF = aksi unduh sekunder → utility gelap, bukan hijau/emoji.
             js-file-download + download: biar nprogress tidak stuck. --}}
        <a href="{{ route('karyawan.slip-gaji.download', $payslip) }}"
            class="btn btn-utility self-start shrink-0 js-file-download"
            data-download="true"
            download>
            <i data-lucide="download" class="ui-icon ui-icon-sm" aria-hidden="true"></i>
            Download PDF
        </a>
    </div>

    {{-- =====================================================
         BAGIAN 2: Data karyawan
         Grid 4 kolom di desktop → isi merata, tidak sempit kiri
         ===================================================== --}}
    <div class="ui-card ui-card-pad mb-4">
        <p class="section-label mb-4">Data Karyawan</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-ink-muted-48 mb-1">Nama</p>
                <p class="text-sm font-semibold text-ink">{{ $payslip->employee->nama }}</p>
            </div>
            <div>
                <p class="text-xs text-ink-muted-48 mb-1">NIK</p>
                <p class="text-sm font-medium text-ink font-mono">{{ $payslip->employee->nik }}</p>
            </div>
            <div>
                <p class="text-xs text-ink-muted-48 mb-1">Jabatan</p>
                <p class="text-sm font-medium text-ink">{{ $payslip->employee->jabatan }}</p>
            </div>
            <div>
                <p class="text-xs text-ink-muted-48 mb-1">Departemen</p>
                <p class="text-sm font-medium text-ink">{{ $payslip->employee->departemen }}</p>
            </div>
        </div>
    </div>

    {{-- =====================================================
         BAGIAN 3: Pendapatan | Potongan (side by side)
         lg:grid-cols-2 biar kanan-kiri seimbang di layar lebar
         ===================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        {{-- Kolom kiri: rincian pendapatan --}}
        <div class="ui-card ui-card-pad">
            <p class="section-label mb-4">Rincian Pendapatan</p>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-ink-muted-80">Gaji Pokok</span>
                    <span class="font-medium text-ink tabular-nums shrink-0">
                        Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Loop tunjangan dari payslip components --}}
                @foreach($payslip->components->where('tipe', 'tunjangan') as $komponen)
                    <div class="flex justify-between gap-4">
                        <span class="text-ink-muted-80">
                            + {{ $komponen->keterangan ?: $komponen->nama_komponen }}
                        </span>
                        <span class="font-medium text-emerald-700 tabular-nums shrink-0">
                            Rp {{ number_format($komponen->nilai, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach

                <div class="flex justify-between gap-4 border-t border-divider-soft pt-2.5 font-semibold">
                    <span class="text-ink">Total Pendapatan (Bruto)</span>
                    <span class="text-ink tabular-nums shrink-0">
                        Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: rekap absensi + potongan --}}
        <div class="ui-card ui-card-pad">
            <p class="section-label mb-4">Rincian Potongan</p>

            {{--
                detail_json = snapshot kehadiran saat payroll dihitung.
                Bukan query absensi live, biar angka slip tidak berubah
                kalau data absensi dikoreksi setelah finalisasi.
            --}}
            @if($payslip->detail_json)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4 rounded-lg bg-canvas-parchment border border-hairline p-3 text-center">
                    <div>
                        <p class="text-base font-semibold text-ink tabular-nums">
                            {{ $payslip->detail_json['total_hadir'] ?? 0 }}
                        </p>
                        <p class="text-[11px] text-ink-muted-48 mt-0.5">Hadir</p>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-amber-600 tabular-nums">
                            {{ $payslip->detail_json['total_telat'] ?? 0 }}
                        </p>
                        <p class="text-[11px] text-ink-muted-48 mt-0.5">Telat</p>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-red-600 tabular-nums">
                            {{ $payslip->detail_json['total_alpha'] ?? 0 }}
                        </p>
                        <p class="text-[11px] text-ink-muted-48 mt-0.5">Alpha</p>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-ink tabular-nums">
                            {{ $payslip->detail_json['total_menit_telat'] ?? 0 }}
                        </p>
                        <p class="text-[11px] text-ink-muted-48 mt-0.5">Menit Telat</p>
                    </div>
                </div>
            @endif

            <div class="space-y-2.5 text-sm">
                @foreach($payslip->components->where('tipe', 'potongan') as $komponen)
                    <div class="flex justify-between gap-4">
                        <span class="text-ink-muted-80">
                            − {{ $komponen->keterangan ?: $komponen->nama_komponen }}
                        </span>
                        <span class="font-medium text-red-600 tabular-nums shrink-0">
                            Rp {{ number_format($komponen->nilai, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach

                <div class="flex justify-between gap-4 border-t border-divider-soft pt-2.5 font-semibold">
                    <span class="text-ink">Total Potongan</span>
                    <span class="text-red-600 tabular-nums shrink-0">
                        Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
         BAGIAN 4: Take-home pay
         Full width biar jadi penutup visual yang jelas
         ===================================================== --}}
    <div class="slip-netpay">
        <div class="min-w-0">
            <p class="slip-netpay-label">Take-Home Pay (Gaji Bersih)</p>
            <p class="slip-netpay-amount">{{ $payslip->gaji_bersih_format }}</p>
        </div>
        <div class="slip-netpay-meta">
            <p class="font-medium text-white">{{ $payslip->payrollPeriod->label }}</p>
            <p class="mt-1">Bank: {{ $payslip->employee->nama_bank ?? '—' }}</p>
            <p>Rek: {{ $payslip->employee->no_rekening ?? '—' }}</p>
        </div>
    </div>
@endsection
