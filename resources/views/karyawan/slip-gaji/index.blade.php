@extends('layouts.app')

@section('title', 'Slip Gaji Saya')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Slip Gaji Saya</h1>
        <p class="text-gray-500 text-sm mt-1">Riwayat slip gaji yang sudah diterbitkan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($payslips as $payslip)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900">{{ $payslip->payrollPeriod->label }}</h3>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Final</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $payslip->gaji_bersih_format }}</p>
                <p class="text-xs text-gray-400 mt-1">Take-Home Pay</p>

                <div class="mt-3 pt-3 border-t border-gray-100 flex gap-2">
                    <a href="{{ route('karyawan.slip-gaji.show', $payslip) }}"
                        class="flex-1 text-center text-sm text-blue-600 hover:text-blue-800 font-medium py-1.5 border border-blue-200 rounded-lg hover:bg-blue-50">
                        Lihat Detail
                    </a>
                    <a href="{{ route('karyawan.slip-gaji.download', $payslip) }}"
                        class="flex-1 text-center text-sm text-gray-600 hover:text-gray-800 font-medium py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">
                        📥 Download PDF
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-16 text-gray-400">
                <p class="text-4xl mb-3">📄</p>
                <p>Belum ada slip gaji yang tersedia.</p>
                <p class="text-sm mt-1">Slip gaji akan muncul setelah HRD memfinalisasi payroll.</p>
            </div>
        @endforelse
    </div>

    @if($payslips->hasPages())
        <div class="mt-4">{{ $payslips->links() }}</div>
    @endif
@endsection
