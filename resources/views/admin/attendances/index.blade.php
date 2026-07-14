@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Admin melihat semua data absensi karyawan dalam satu bulan.
        Bisa difilter berdasarkan: bulan, tahun, karyawan tertentu, dan status.
        Admin juga bisa koreksi data absensi yang salah.
    --}}

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekap Absensi</h1>
            <p class="text-gray-500 text-sm mt-1">
                Data kehadiran karyawan bulan
                {{-- Format nama bulan dari angka (1-12) ke nama bulan Indonesia --}}
                {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
            </p>
        </div>
    </div>

    {{-- =====================================================
         FILTER: Bulan, Tahun, Karyawan, dan Status
         Form ini mengirim GET request ke URL yang sama
         dengan tambahan query string (?bulan=7&tahun=2026&...)
         ===================================================== --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
            <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{-- Konversi angka bulan ke nama bulan --}}
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
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Karyawan</label>
            <select name="employee_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 w-48">
                <option value="">Semua Karyawan</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="telat" {{ request('status') === 'telat' ? 'selected' : '' }}>Telat</option>
                <option value="alpha" {{ request('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
            Filter
        </button>
        <a href="{{ route('admin.absensi.index') }}" class="text-gray-400 hover:text-gray-600 px-2 py-2 text-sm">
            Reset
        </a>
    </form>

    {{-- =====================================================
         TABEL DATA ABSENSI
         Menampilkan data absensi dengan paginasi 20 baris per halaman
         ===================================================== --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Karyawan</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Check-in</th>
                    <th class="px-6 py-3 text-left">Check-out</th>
                    <th class="px-6 py-3 text-right">Menit Telat</th>
                    <th class="px-6 py-3 text-center">Koreksi?</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-600">
                            {{-- Format tanggal: "Sen, 14 Jul 2026" --}}
                            {{ $attendance->tanggal->translatedFormat('D, d M Y') }}
                        </td>
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-900">{{ $attendance->employee->nama }}</p>
                            <p class="text-xs text-gray-400">{{ $attendance->employee->departemen }}</p>
                        </td>
                        <td class="px-6 py-3">
                            {{-- Badge warna berbeda untuk setiap status --}}
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($attendance->status === 'hadir') bg-green-100 text-green-700
                                @elseif($attendance->status === 'telat') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-600
                                @endif">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 font-mono text-xs text-gray-600">
                            {{-- Optional chaining (?->): jika waktu null, tampilkan "-" --}}
                            {{ $attendance->waktu_checkin?->format('H:i') ?? '-' }}
                            {{-- Link GPS Lokasi Check-in --}}
                            @if($attendance->latitude_checkin && $attendance->longitude_checkin)
                                <a href="https://www.google.com/maps?q={{ $attendance->latitude_checkin }},{{ $attendance->longitude_checkin }}"
                                   target="_blank"
                                   title="Lihat lokasi check-in di Google Maps"
                                   class="ml-1 text-blue-500 hover:text-blue-700 text-xs">📍 Map</a>
                            @endif
                        </td>
                        <td class="px-6 py-3 font-mono text-xs text-gray-600">
                            {{ $attendance->waktu_checkout?->format('H:i') ?? '-' }}
                            {{-- Link GPS Lokasi Check-out --}}
                            @if($attendance->latitude_checkout && $attendance->longitude_checkout)
                                <a href="https://www.google.com/maps?q={{ $attendance->latitude_checkout }},{{ $attendance->longitude_checkout }}"
                                   target="_blank"
                                   title="Lihat lokasi check-out di Google Maps"
                                   class="ml-1 text-blue-500 hover:text-blue-700 text-xs">📍 Map</a>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right text-xs">
                            @if($attendance->menit_terlambat > 0)
                                <span class="text-yellow-600 font-medium">{{ $attendance->menit_terlambat }} mnt</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            {{-- is_koreksi = true jika data sudah pernah dikoreksi admin --}}
                            @if($attendance->is_koreksi)
                                <span class="text-xs text-blue-500 font-medium">✎ Sudah dikoreksi</span>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            {{-- Link koreksi: admin bisa ubah data absensi yang salah --}}
                            <a href="{{ route('admin.absensi.koreksi', $attendance) }}"
                                class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                Koreksi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <div class="text-4xl mb-2">📋</div>
                            Tidak ada data absensi untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginasi: tampilkan navigasi halaman jika data lebih dari 20 baris --}}
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
@endsection
