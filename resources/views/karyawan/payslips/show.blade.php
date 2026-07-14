@extends('layouts.app')

@section('title', 'Detail Slip Gaji ' . $payslip->payrollPeriod->label)

@section('content')
    <div class="mb-6">
        <a href="{{ route('karyawan.slip-gaji.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        <div class="flex items-center justify-between mt-2">
            <h1 class="text-2xl font-bold text-gray-900">Slip Gaji — {{ $payslip->payrollPeriod->label }}</h1>
            <a href="{{ route('karyawan.slip-gaji.download', $payslip) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm">
                📥 Download PDF
            </a>
        </div>
    </div>

    <div class="max-w-2xl space-y-4">
        {{-- Info Karyawan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Karyawan</h2>
            <div class="grid grid-cols-2 gap-y-2 text-sm">
                <div class="text-gray-500">Nama</div><div class="font-medium">{{ $payslip->employee->nama }}</div>
                <div class="text-gray-500">NIK</div><div>{{ $payslip->employee->nik }}</div>
                <div class="text-gray-500">Jabatan</div><div>{{ $payslip->employee->jabatan }}</div>
                <div class="text-gray-500">Departemen</div><div>{{ $payslip->employee->departemen }}</div>
            </div>
        </div>

        {{-- Rincian Gaji --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Rincian Pendapatan</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Gaji Pokok</span>
                    <span class="font-medium">Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}</span>
                </div>
                @foreach($payslip->components->where('tipe', 'tunjangan') as $komponen)
                    <div class="flex justify-between text-green-700">
                        <span>+ {{ $komponen->nama_komponen }}</span>
                        <span>Rp {{ number_format($komponen->nilai, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-semibold border-t border-gray-100 pt-2">
                    <span>Total Pendapatan (Gaji Bruto)</span>
                    <span>Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Rincian Potongan</h2>

            {{-- Rekap Kehadiran --}}
            @if($payslip->detail_json)
                <div class="bg-gray-50 rounded-lg p-3 mb-3 text-xs text-gray-600 grid grid-cols-4 gap-2 text-center">
                    <div><p class="font-bold text-lg text-gray-800">{{ $payslip->detail_json['total_hadir'] ?? 0 }}</p><p>Hadir</p></div>
                    <div><p class="font-bold text-lg text-yellow-600">{{ $payslip->detail_json['total_telat'] ?? 0 }}</p><p>Telat</p></div>
                    <div><p class="font-bold text-lg text-red-600">{{ $payslip->detail_json['total_alpha'] ?? 0 }}</p><p>Alpha</p></div>
                    <div><p class="font-bold text-lg text-gray-800">{{ $payslip->detail_json['total_menit_telat'] ?? 0 }}</p><p>Menit Telat</p></div>
                </div>
            @endif

            <div class="space-y-2 text-sm">
                @foreach($payslip->components->where('tipe', 'potongan') as $komponen)
                    <div class="flex justify-between text-red-600">
                        <span>- {{ $komponen->nama_komponen }}</span>
                        <span>Rp {{ number_format($komponen->nilai, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-semibold border-t border-gray-100 pt-2 text-red-600">
                    <span>Total Potongan</span>
                    <span>Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Gaji Bersih --}}
        <div class="bg-blue-700 rounded-xl p-6 text-white flex justify-between items-center">
            <div>
                <p class="text-blue-200 text-sm">Take-Home Pay (Gaji Bersih)</p>
                <p class="text-3xl font-bold mt-1">{{ $payslip->gaji_bersih_format }}</p>
            </div>
            <div class="text-blue-200 text-sm text-right">
                <p>{{ $payslip->payrollPeriod->label }}</p>
                <p class="mt-1">Bank: {{ $payslip->employee->nama_bank ?? '-' }}</p>
                <p>Rek: {{ $payslip->employee->no_rekening ?? '-' }}</p>
            </div>
        </div>
    </div>
@endsection
