@extends('layouts.app')

@section('title', 'Detail Payroll ' . $payrollPeriod->label)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.payroll.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        <div class="flex items-center justify-between mt-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payroll: {{ $payrollPeriod->label }}</h1>
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
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm text-center">
                <p class="text-xs text-gray-500">Total Karyawan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $payrollPeriod->payslips->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm text-center">
                <p class="text-xs text-gray-500">Total Gaji Bruto</p>
                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($payrollPeriod->payslips->sum('gaji_bruto'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm text-center">
                <p class="text-xs text-gray-500">Total Gaji Bersih</p>
                <p class="text-xl font-bold text-green-600">Rp {{ number_format($payrollPeriod->payslips->sum('gaji_bersih'), 0, ',', '.') }}</p>
            </div>
        </div>
    @endif

    {{-- Tabel Slip Gaji Per Karyawan --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Daftar Slip Gaji Karyawan</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Karyawan</th>
                    <th class="px-6 py-3 text-right">Gaji Bruto</th>
                    <th class="px-6 py-3 text-right">Potongan Absensi</th>
                    <th class="px-6 py-3 text-right">Potongan Pajak</th>
                    <th class="px-6 py-3 text-right">Total Potongan</th>
                    <th class="px-6 py-3 text-right">Gaji Bersih</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payrollPeriod->payslips as $payslip)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium">{{ $payslip->employee->nama }}</p>
                            <p class="text-xs text-gray-400">{{ $payslip->employee->jabatan }} — {{ $payslip->employee->departemen }}</p>
                        </td>
                        <td class="px-6 py-3 text-right">Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-red-600">Rp {{ number_format($payslip->potongan_absensi, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right">
                            @if(!$payrollPeriod->isFinal())
                                {{-- Input potongan pajak manual HRD --}}
                                <form method="POST" action="{{ route('admin.slip-gaji.potongan-pajak', $payslip) }}" class="flex items-center gap-1 justify-end">
                                    @csrf
                                    <input type="number" name="potongan_pajak" value="{{ $payslip->potongan_pajak }}" min="0" step="1000"
                                        class="w-28 border border-gray-300 rounded px-2 py-1 text-xs text-right">
                                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs">Simpan</button>
                                </form>
                            @else
                                <span class="text-red-600">Rp {{ number_format($payslip->potongan_pajak, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right text-red-600 font-medium">Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right font-bold text-green-700">Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}</td>
                        <td class="px-6 py-3">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('admin.slip-gaji.show', $payslip) }}"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">Detail</a>
                                <a href="{{ route('admin.slip-gaji.download', $payslip) }}"
                                    class="text-gray-600 hover:text-gray-800 text-xs font-medium">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            Belum ada data. Klik "Proses Payroll" untuk menghitung gaji karyawan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
