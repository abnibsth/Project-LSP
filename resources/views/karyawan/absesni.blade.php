@extends('layouts.app')

@section('title', 'Absensi Saya')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Karyawan melakukan check-in dan check-out harian.
    --}}

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Absensi Saya</h1>
        <p class="text-gray-500 text-sm mt-1">Lakukan check-in dan check-out harian di sini.</p>
    </div>

    {{-- Tombol Check-In / Check-Out --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-6">
        <div class="text-center">
            <p class="text-lg font-semibold text-gray-800">{{ now()->translatedFormat('l, d F Y') }}</p>
            <p class="text-4xl font-bold text-gray-900 mt-1" id="jam-sekarang">{{ now()->format('H:i:s') }}</p>

            <div class="mt-4 text-sm text-gray-500">
                Jam Kerja: {{ $rule->jam_masuk }} — {{ $rule->jam_keluar }}
                (Toleransi {{ $rule->toleransi_menit }} menit)
            </div>

            <div class="mt-6 flex gap-3 justify-center">
                @if(!$absensiHariIni)
                    {{-- Form Check-In --}}
                    <form method="POST" action="{{ route('karyawan.absensi.checkin') }}" id="form-checkin">
                        @csrf
                        <button type="submit" id="btn-checkin"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-xl text-base transition-colors shadow">
                            ✅ Check-In Sekarang
                        </button>
                    </form>
                @elseif(!$absensiHariIni->waktu_checkout)
                    <div class="text-green-700 bg-green-50 px-4 py-2 rounded-lg text-sm font-medium mr-3 flex items-center gap-1">
                        <span>✅</span> Check-in: {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                        | Status: {{ ucfirst($absensiHariIni->status) }}
                    </div>
                    {{-- Form Check-Out --}}
                    <form method="POST" action="{{ route('karyawan.absensi.checkout') }}" id="form-checkout">
                        @csrf
                        <button type="submit" id="btn-checkout"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-xl text-base transition-colors shadow">
                            🚪 Check-Out
                        </button>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 px-6 py-3 rounded-xl text-sm font-medium">
                        🏁 Selesai hari ini — Masuk: {{ $absensiHariIni->waktu_checkin->format('H:i') }} |
                        Pulang: {{ $absensiHariIni->waktu_checkout->format('H:i') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Filter Bulan --}}
    <form method="GET" class="flex gap-3 mb-4">
        <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
        <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            @foreach(range(date('Y'), date('Y') - 3) as $y)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            Filter
        </button>
    </form>

    {{-- Tabel Rekap Absensi --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Rekap Kehadiran</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[650px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Check-In</th>
                        <th class="px-6 py-3 text-left">Check-Out</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekapAbsensi as $absensi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ $absensi->tanggal->translatedFormat('d F Y') }}</td>
                            <td class="px-6 py-3">
                                {{ $absensi->waktu_checkin?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-3">
                                {{ $absensi->waktu_checkout?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($absensi->status === 'hadir') bg-green-100 text-green-700
                                    @elseif($absensi->status === 'telat') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($absensi->status) }}
                                    @if($absensi->status === 'telat')
                                        ({{ $absensi->menit_terlambat }} menit)
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-400">
                                @if($absensi->is_koreksi)
                                    <span class="text-orange-500">✏️ Dikoreksi admin</span>
                                @endif
                                {{ $absensi->keterangan ?? '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                Tidak ada data absensi untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Jam Live Clock
            setInterval(() => {
                const clock = document.getElementById('jam-sekarang');
                if (clock) {
                    const now = new Date();
                    clock.textContent = now.toTimeString().split(' ')[0];
                }
            }, 1000);
        });
    </script>
@endsection
