@extends('layouts.app')

@section('title', 'Absensi Saya')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Karyawan melakukan check-in dan check-out harian.
        Fitur Keamanan Baru: Mendeteksi lokasi GPS (latitude & longitude) secara real-time
        sebelum karyawan menekan tombol check-in/out untuk memverifikasi lokasi kerja.
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

            {{-- INDIKATOR DETEKSI LOKASI GPS --}}
            <div class="mt-3 text-xs text-amber-600 flex justify-center items-center gap-1.5" id="location-status">
                <span class="animate-spin inline-block w-3 h-3 border-2 border-amber-600 border-t-transparent rounded-full"></span>
                Sedang mendeteksi lokasi GPS Anda... (Izinkan akses lokasi jika diminta browser)
            </div>

            <div class="mt-6 flex gap-3 justify-center">
                @if(!$absensiHariIni)
                    {{-- Form Check-In dengan Hidden Fields untuk latitude/longitude --}}
                    <form method="POST" action="{{ route('karyawan.absensi.checkin') }}" id="form-checkin">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude-checkin">
                        <input type="hidden" name="longitude" id="longitude-checkin">
                        <button type="submit" id="btn-checkin"
                            class="bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold px-8 py-3 rounded-xl text-base transition-colors shadow">
                            ✅ Check-In Sekarang
                        </button>
                    </form>
                @elseif(!$absensiHariIni->waktu_checkout)
                    <div class="text-green-700 bg-green-50 px-4 py-2 rounded-lg text-sm font-medium mr-3 flex items-center gap-1">
                        <span>✅</span> Check-in: {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                        | Status: {{ ucfirst($absensiHariIni->status) }}
                    </div>
                    {{-- Form Check-Out dengan Hidden Fields untuk latitude/longitude --}}
                    <form method="POST" action="{{ route('karyawan.absensi.checkout') }}" id="form-checkout">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude-checkout">
                        <input type="hidden" name="longitude" id="longitude-checkout">
                        <button type="submit" id="btn-checkout"
                            class="bg-orange-500 hover:bg-orange-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold px-8 py-3 rounded-xl text-base transition-colors shadow">
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
                                {{-- Jika ada lokasi check-in, tampilkan icon maps link --}}
                                @if($absensi->latitude_checkin && $absensi->longitude_checkin)
                                    <a href="https://www.google.com/maps?q={{ $absensi->latitude_checkin }},{{ $absensi->longitude_checkin }}"
                                       target="_blank"
                                       title="Lihat lokasi check-in di Google Maps"
                                       class="ml-1 text-blue-500 hover:text-blue-700 text-xs">📍 Lokasi</a>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                {{ $absensi->waktu_checkout?->format('H:i') ?? '-' }}
                                {{-- Jika ada lokasi check-out, tampilkan icon maps link --}}
                                @if($absensi->latitude_checkout && $absensi->longitude_checkout)
                                    <a href="https://www.google.com/maps?q={{ $absensi->latitude_checkout }},{{ $absensi->longitude_checkout }}"
                                       target="_blank"
                                       title="Lihat lokasi check-out di Google Maps"
                                       class="ml-1 text-blue-500 hover:text-blue-700 text-xs">📍 Lokasi</a>
                                @endif
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

    {{-- SCRIPT JAVASCRIPT GEOLOCATION DETECTOR --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const latCheckin = document.getElementById('latitude-checkin');
            const lngCheckin = document.getElementById('longitude-checkin');
            const latCheckout = document.getElementById('latitude-checkout');
            const lngCheckout = document.getElementById('longitude-checkout');
            
            const btnCheckin = document.getElementById('btn-checkin');
            const btnCheckout = document.getElementById('btn-checkout');
            const locationStatus = document.getElementById('location-status');

            // Nonaktifkan tombol absensi sebelum lokasi GPS berhasil dideteksi
            if (btnCheckin) btnCheckin.disabled = true;
            if (btnCheckout) btnCheckout.disabled = true;

            // Meminta akses Geolocation API browser
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        // Set koordinat ke dalam input form check-in
                        if (latCheckin && lngCheckin) {
                            latCheckin.value = lat;
                            lngCheckin.value = lng;
                        }

                        // Set koordinat ke dalam input form check-out
                        if (latCheckout && lngCheckout) {
                            latCheckout.value = lat;
                            lngCheckout.value = lng;
                        }

                        // Tampilkan status sukses deteksi lokasi
                        if (locationStatus) {
                            locationStatus.innerHTML = `📍 GPS Aktif: <span class="font-mono text-green-600 font-semibold">${lat.toFixed(6)}, ${lng.toFixed(6)}</span>`;
                            locationStatus.className = "mt-3 text-xs text-green-600 flex justify-center items-center gap-1.5";
                        }

                        // Aktifkan kembali tombol absensi setelah koordinat didapatkan
                        if (btnCheckin) btnCheckin.disabled = false;
                        if (btnCheckout) btnCheckout.disabled = false;
                    },
                    function(error) {
                        console.error("GPS Error:", error);
                        let errMsg = "Harap izinkan akses lokasi (GPS) pada browser Anda untuk absen.";
                        
                        if (error.code === error.PERMISSION_DENIED) {
                            errMsg = "Akses GPS ditolak! Anda wajib mengizinkan lokasi untuk melakukan absensi.";
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            errMsg = "Informasi lokasi GPS tidak tersedia. Pastikan GPS HP/Komputer aktif.";
                        } else if (error.code === error.TIMEOUT) {
                            errMsg = "Waktu deteksi GPS habis. Silakan refresh halaman.";
                        }

                        if (locationStatus) {
                            locationStatus.innerHTML = `❌ ${errMsg}`;
                            locationStatus.className = "mt-3 text-xs text-red-600 font-semibold flex justify-center items-center gap-1.5";
                        }
                    },
                    {
                        enableHighAccuracy: true, // Gunakan akurasi terbaik (GPS HP/WiFi)
                        timeout: 10000,          // Batas deteksi 10 detik
                        maximumAge: 0            // Selalu minta data terbaru (bukan cache)
                    }
                );
            } else {
                if (locationStatus) {
                    locationStatus.innerHTML = "❌ Browser Anda tidak mendukung pendeteksian lokasi GPS.";
                    locationStatus.className = "mt-3 text-xs text-red-600 font-semibold flex justify-center items-center gap-1.5";
                }
            }

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
