@extends('layouts.app')

@section('title', 'Slip Gaji')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Admin melihat daftar semua slip gaji karyawan dari periode yang sudah FINAL.
        Periode yang masih draft tidak ditampilkan di sini.
        Admin bisa klik "Detail" untuk melihat rincian, atau "Download" untuk cetak PDF.
    --}}

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Slip Gaji Karyawan</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar slip gaji dari semua periode yang sudah difinalisasi.</p>
        </div>
        {{-- Shortcut ke proses payroll --}}
        <a href="{{ route('admin.payroll.index') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
            🔄 Ke Proses Payroll
        </a>
    </div>

    {{-- =====================================================
         TABEL DAFTAR SLIP GAJI
         Hanya menampilkan slip dari periode yang STATUS = 'final'
         (difilter di controller: whereHas('payrollPeriod', fn($q) => $q->where('status', 'final')))
         ===================================================== --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Karyawan</th>
                    <th class="px-6 py-3 text-left">Departemen</th>
                    <th class="px-6 py-3 text-left">Periode</th>
                    <th class="px-6 py-3 text-right">Gaji Pokok</th>
                    <th class="px-6 py-3 text-right">Total Tunjangan</th>
                    <th class="px-6 py-3 text-right">Total Potongan</th>
                    <th class="px-6 py-3 text-right">Gaji Bersih</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payslips as $payslip)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-900">{{ $payslip->employee->nama }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $payslip->employee->nik }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ $payslip->employee->departemen }}
                        </td>
                        <td class="px-6 py-3">
                            {{-- label = properti accessor dari model PayrollPeriod, misal: "Juli 2026" --}}
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $payslip->payrollPeriod->label }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right text-gray-600">
                            Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-right text-green-600 font-medium">
                            + Rp {{ number_format($payslip->total_tunjangan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-right text-red-500 font-medium">
                            - Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-right font-bold text-gray-900">
                            Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex gap-1.5 justify-center items-center">
                                {{-- Lihat detail slip gaji --}}
                                <a href="{{ route('admin.slip-gaji.show', $payslip) }}"
                                    class="px-2.5 py-1 bg-primary/10 text-primary hover:bg-primary/20 transition-all rounded-sm text-[10px] font-bold active:scale-95">
                                    Detail
                                </a>
                                {{-- Download sebagai PDF menggunakan barryvdh/laravel-dompdf --}}
                                <a href="{{ route('admin.slip-gaji.download', $payslip) }}"
                                    class="px-2.5 py-1 bg-green-50 text-green-700 hover:bg-green-100 transition-all rounded-sm text-[10px] font-bold active:scale-95">
                                    PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <div class="text-4xl mb-2">📄</div>
                            <p>Belum ada slip gaji yang tersedia.</p>
                            <p class="text-xs mt-1">
                                Jalankan proses payroll dan finalisasi terlebih dahulu.
                                <a href="{{ route('admin.payroll.index') }}" class="text-blue-600 ml-1">Ke Payroll →</a>
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginasi --}}
        @if($payslips->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $payslips->links() }}
            </div>
        @endif
    </div>
@endsection
