@extends('layouts.app')

@section('title', 'Detail Payroll ' . $payrollPeriod->label)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.payroll.index') }}" class="text-sm text-ink-muted-48 hover:text-ink-muted-80">← Kembali</a>
        <div class="flex items-center justify-between mt-2">
            <div>
                <h1 class="page-title">Payroll: {{ $payrollPeriod->label }}</h1>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $payrollPeriod->status === 'final' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $payrollPeriod->status === 'final' ? '🔒 Final' : '✏️ Draft' }}
                </span>
            </div>
            @if(!$payrollPeriod->isFinal())
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('admin.payroll.proses', $payrollPeriod) }}"
                        onsubmit="return confirm('Proses/hitung ulang payroll {{ $payrollPeriod->label }}?')">
                        @csrf
                        <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-medium px-4 py-2 rounded-lg text-sm">
                            🔄 Proses Payroll
                        </button>
                    </form>
                    @if($payrollPeriod->payslips->count() > 0)
                        <form method="POST" action="{{ route('admin.payroll.finalisasi', $payrollPeriod) }}"
                            onsubmit="return confirm('Finalisasi payroll {{ $payrollPeriod->label }}? Data akan dikunci permanen!')">
                            @csrf
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg text-sm">
                                ✅ Finalisasi
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Statistik Total --}}
    @if($payrollPeriod->payslips->count() > 0)
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-hairline p-4 shadow-sm text-center">
                <p class="text-xs text-ink-muted-48">Total Karyawan</p>
                <p class="page-title">{{ $payrollPeriod->payslips->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-hairline p-4 shadow-sm text-center">
                <p class="text-xs text-ink-muted-48">Total Gaji Bruto</p>
                <p class="text-xl font-bold text-ink">Rp {{ number_format($payrollPeriod->payslips->sum('gaji_bruto'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-hairline p-4 shadow-sm text-center">
                <p class="text-xs text-ink-muted-48">Total Gaji Bersih</p>
                <p class="text-xl font-bold text-green-600">Rp {{ number_format($payrollPeriod->payslips->sum('gaji_bersih'), 0, ',', '.') }}</p>
            </div>
        </div>
    @endif

    {{-- Tabel Slip Gaji Per Karyawan --}}
    <div class="bg-white rounded-xl border border-hairline shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-divider-soft flex items-center justify-between">
            <h2 class="font-semibold text-ink">Daftar Slip Gaji Karyawan</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-canvas-parchment text-ink-muted-48 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Karyawan</th>
                    <th class="px-6 py-3 text-right">Gaji Bruto</th>
                    <th class="px-6 py-3 text-right">Total Potongan</th>
                    <th class="px-6 py-3 text-right">Gaji Bersih</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-divider-soft">
                @forelse($payrollPeriod->payslips as $payslip)
                    <tr class="hover:bg-canvas-parchment">
                        <td class="px-6 py-3">
                            <p class="font-medium">{{ $payslip->employee->nama }}</p>
                            <p class="text-xs text-ink-muted-48">{{ $payslip->employee->jabatan }} — {{ $payslip->employee->departemen }}</p>
                        </td>
                        <td class="px-6 py-3 text-right">Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-red-600 font-medium">Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right font-bold text-green-700">Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}</td>
                        <td class="px-6 py-3">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('admin.slip-gaji.show', $payslip) }}"
                                    class="text-primary hover:text-blue-800 text-xs font-medium">Detail</a>
                                <a href="{{ route('admin.slip-gaji.download', $payslip) }}"
                                    class="text-ink-muted-80 hover:text-ink text-xs font-medium">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-ink-muted-48">
                            Belum ada data. Klik "Proses Payroll" untuk menghitung gaji karyawan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
