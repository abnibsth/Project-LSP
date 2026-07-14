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

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Payroll</h1>
            <p class="text-gray-500 text-sm mt-1">Rekap pengeluaran gaji bulanan.</p>
        </div>
    </div>

    {{-- Filter Bulan & Tahun --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
            <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
            <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
            Tampilkan
        </button>
    </form>

    @if($periode)
        {{-- Kartu Statistik Total --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xs text-gray-500 mb-1">Total Karyawan</p>
                <p class="text-3xl font-bold text-gray-900">{{ $periode->payslips->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xs text-gray-500 mb-1">Total Gaji Bruto</p>
                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($totalGajiBruto, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xs text-gray-500 mb-1">Total Gaji Bersih (THP)</p>
                <p class="text-xl font-bold text-green-600">Rp {{ number_format($totalGajiBersih, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Tabel Rincian Per Karyawan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">
                    Rincian Payroll — {{ $periode->label }}
                </h2>
                <span class="px-2.5 py-1 text-xs rounded-full font-medium
                    {{ $periode->status === 'final' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $periode->status === 'final' ? '🔒 Final' : '✏️ Draft' }}
                </span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Karyawan</th>
                        <th class="px-6 py-3 text-left">Departemen</th>
                        <th class="px-6 py-3 text-right">Gaji Bruto</th>
                        <th class="px-6 py-3 text-right">Total Potongan</th>
                        <th class="px-6 py-3 text-right">Gaji Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($periode->payslips as $slip)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <p class="font-medium text-gray-900">{{ $slip->employee->nama }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $slip->employee->nik }}</p>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $slip->employee->departemen }}</td>
                            <td class="px-6 py-3 text-right">Rp {{ number_format($slip->gaji_bruto, 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-right text-red-500">- Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-right font-bold text-green-600">Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    {{-- Baris Total --}}
                    <tr class="bg-gray-50 font-semibold border-t-2 border-gray-200">
                        <td class="px-6 py-3" colspan="2">TOTAL</td>
                        <td class="px-6 py-3 text-right">Rp {{ number_format($totalGajiBruto, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-red-500">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-green-600">Rp {{ number_format($totalGajiBersih, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        {{-- Jika belum ada periode payroll untuk bulan ini --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
            <div class="text-5xl mb-3">📊</div>
            <p class="text-gray-500">
                Belum ada data payroll untuk
                <strong>{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</strong>.
            </p>
            <a href="{{ route('admin.payroll.index') }}" class="text-blue-600 text-sm mt-2 inline-block">
                Ke halaman Proses Payroll →
            </a>
        </div>
    @endif
@endsection
