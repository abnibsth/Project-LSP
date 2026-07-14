@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Menampilkan laporan rekap kehadiran semua karyawan dalam satu bulan.
        withCount() di controller menghitung hadir/telat/alpha per karyawan
        tanpa harus load semua data absensi (lebih efisien di database).
    --}}

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Absensi</h1>
            <p class="text-gray-500 text-sm mt-1">Rekap kehadiran karyawan per bulan.</p>
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

    {{-- Tabel Rekap Absensi Per Karyawan --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">
                Rekap Kehadiran — {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
            </h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Karyawan</th>
                    <th class="px-6 py-3 text-left">Departemen</th>
                    {{-- Kolom ini diisi dari withCount() di controller --}}
                    <th class="px-6 py-3 text-center">✅ Hadir</th>
                    <th class="px-6 py-3 text-center">⏰ Telat</th>
                    <th class="px-6 py-3 text-center">❌ Alpha</th>
                    <th class="px-6 py-3 text-center">Total Kehadiran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-900">{{ $emp->nama }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $emp->nik }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $emp->departemen }}</td>
                        <td class="px-6 py-3 text-center">
                            {{-- total_hadir adalah hasil withCount() di controller --}}
                            <span class="font-medium text-green-600">{{ $emp->total_hadir }}</span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="font-medium text-yellow-600">{{ $emp->total_telat }}</span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="font-medium text-red-600">{{ $emp->total_alpha }}</span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            {{-- Total kehadiran = hadir + telat (keduanya masuk kerja, hanya beda waktu) --}}
                            <span class="font-bold text-gray-900">
                                {{ $emp->total_hadir + $emp->total_telat }} hari
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            Tidak ada data karyawan aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
