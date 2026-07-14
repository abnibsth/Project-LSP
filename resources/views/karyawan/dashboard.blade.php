@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Saya</h1>
        <p class="text-gray-500 text-sm mt-1">Selamat datang, {{ auth()->user()->name }}.</p>
    </div>

    @if(!$employee)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg px-4 py-3">
            ⚠️ Data karyawan belum terhubung ke akun ini. Hubungi Admin/HRD.
        </div>
    @else
        {{-- Status Absensi Hari Ini --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-4">
            <h2 class="text-base font-semibold text-gray-900 mb-4">📅 Absensi Hari Ini — {{ now()->translatedFormat('l, d F Y') }}</h2>

            @if(!$absensiHariIni)
                {{-- Belum check-in --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center text-3xl">⏰</div>
                    <div>
                        <p class="font-medium text-gray-800">Belum Check-In</p>
                        <p class="text-sm text-gray-500">Silakan lakukan check-in untuk mencatat kehadiran.</p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkin') }}" class="ml-auto">
                        @csrf
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                            ✅ Check-In Sekarang
                        </button>
                    </form>
                </div>
            @elseif(!$absensiHariIni->waktu_checkout)
                {{-- Sudah check-in, belum check-out --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center text-3xl">✅</div>
                    <div>
                        <p class="font-medium text-gray-800">Sudah Check-In</p>
                        <p class="text-sm text-gray-500">
                            Pukul {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                            — Status:
                            <span class="font-medium {{ $absensiHariIni->status === 'telat' ? 'text-yellow-600' : 'text-green-600' }}">
                                {{ ucfirst($absensiHariIni->status) }}
                                @if($absensiHariIni->status === 'telat')
                                    ({{ $absensiHariIni->menit_terlambat }} menit)
                                @endif
                            </span>
                        </p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkout') }}" class="ml-auto">
                        @csrf
                        <button type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                            🚪 Check-Out
                        </button>
                    </form>
                </div>
            @else
                {{-- Sudah check-out --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center text-3xl">🏁</div>
                    <div>
                        <p class="font-medium text-gray-800">Selesai Hari Ini</p>
                        <p class="text-sm text-gray-500">
                            Masuk: {{ $absensiHariIni->waktu_checkin->format('H:i') }} —
                            Pulang: {{ $absensiHariIni->waktu_checkout->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Ringkasan Kehadiran Bulan Ini --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900 mb-4">📊 Kehadiran Bulan Ini</h2>
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $rekapAbsensi['hadir'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Hadir</p>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600">{{ $rekapAbsensi['telat'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Telat</p>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-lg">
                        <p class="text-2xl font-bold text-red-600">{{ $rekapAbsensi['alpha'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Alpha</p>
                    </div>
                </div>
            </div>

            {{-- Gaji Terakhir --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900 mb-4">💰 Gaji Terakhir</h2>
                @if($slipTerakhir)
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $slipTerakhir->gaji_bersih_format }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $slipTerakhir->payrollPeriod->label }}</p>
                        <a href="{{ route('karyawan.slip-gaji.show', $slipTerakhir) }}"
                            class="inline-block mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Lihat Detail →
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Belum ada slip gaji yang tersedia.</p>
                @endif
            </div>
        </div>
    @endif
@endsection
