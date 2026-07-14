@extends('layouts.app')

@section('title', 'Aturan Absensi')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Form untuk admin mengatur aturan absensi perusahaan.
        Aturan ini digunakan oleh sistem untuk:
        1. Menentukan status karyawan saat check-in (hadir/telat)
        2. Menghitung potongan gaji berdasarkan kehadiran
        Hanya ada SATU aturan yang berlaku di seluruh perusahaan.
    --}}

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Aturan Absensi</h1>
        <p class="text-gray-500 text-sm mt-1">
            Pengaturan jam kerja dan nominal potongan untuk keterlambatan & alpha.
        </p>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.aturan-absensi.update') }}" class="space-y-6">
            {{-- @csrf: Token keamanan wajib ada di setiap form POST --}}
            @csrf
            {{-- @method('PUT'): HTML form hanya bisa GET/POST, ini trick untuk kirim method PUT --}}
            @method('PUT')

            {{-- =====================================================
                 BAGIAN 1: PENGATURAN JAM KERJA
                 Menentukan kapan karyawan dianggap hadir/telat
                 ===================================================== --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    🕐 Jam Kerja
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jam Masuk
                        </label>
                        {{-- old('jam_masuk', $rule->jam_masuk): tampilkan input lama jika form error,
                             atau nilai dari database jika pertama kali buka --}}
                        <input type="time" name="jam_masuk"
                            value="{{ old('jam_masuk', \Illuminate\Support\Str::substr($rule->jam_masuk, 0, 5)) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('jam_masuk') border-red-400 @enderror">
                        @error('jam_masuk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jam Keluar
                        </label>
                        <input type="time" name="jam_keluar"
                            value="{{ old('jam_keluar', \Illuminate\Support\Str::substr($rule->jam_keluar, 0, 5)) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('jam_keluar') border-red-400 @enderror">
                        @error('jam_keluar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Toleransi Keterlambatan
                        </label>
                        {{-- Input Group: Menggabungkan input dengan teks addon di sebelah kanan --}}
                        <div class="flex rounded-lg shadow-sm">
                            <input type="number" name="toleransi_menit" min="0" max="120"
                                value="{{ old('toleransi_menit', $rule->toleransi_menit) }}"
                                class="flex-1 min-w-0 block w-full border border-gray-300 rounded-none rounded-l-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('toleransi_menit') border-red-400 @enderror">
                            <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                menit
                            </span>
                        </div>
                        @error('toleransi_menit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        {{-- Penjelasan: jika check-in dalam toleransi → HADIR, lewat → TELAT --}}
                        <p class="text-xs text-gray-400 mt-1">
                            Check-in dalam {{ $rule->toleransi_menit }} menit setelah jam masuk = HADIR
                        </p>
                    </div>
                </div>
            </div>

            {{-- =====================================================
                 BAGIAN 2: PENGATURAN POTONGAN GAJI
                 Menentukan berapa rupiah yang dipotong per kejadian
                 ===================================================== --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    ✂️ Potongan Gaji
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Potongan per Hari Alpha
                        </label>
                        {{-- Input Group: Menggabungkan addon 'Rp' di kiri dengan input angka --}}
                        <div class="flex rounded-lg shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                Rp
                            </span>
                            <input type="number" name="potongan_per_alpha" min="0" step="1000"
                                value="{{ (int) old('potongan_per_alpha', $rule->potongan_per_alpha) }}"
                                class="flex-1 min-w-0 block w-full border border-gray-300 rounded-none rounded-r-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('potongan_per_alpha') border-red-400 @enderror">
                        </div>
                        @error('potongan_per_alpha')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            Dipotong flat per hari karyawan tidak masuk (alpha)
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Potongan per Menit Telat
                        </label>
                        {{-- Input Group: Menggabungkan addon 'Rp' di kiri dengan input angka --}}
                        <div class="flex rounded-lg shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                Rp
                            </span>
                            <input type="number" name="potongan_per_menit_telat" min="0" step="100"
                                value="{{ (int) old('potongan_per_menit_telat', $rule->potongan_per_menit_telat) }}"
                                class="flex-1 min-w-0 block w-full border border-gray-300 rounded-none rounded-r-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('potongan_per_menit_telat') border-red-400 @enderror">
                        </div>
                        @error('potongan_per_menit_telat')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            Dipotong per menit × total menit terlambat
                        </p>
                    </div>
                </div>

                {{-- Contoh perhitungan supaya admin paham dampak pengaturan --}}
                <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-xs text-amber-700">
                    <strong>💡 Contoh:</strong>
                    Jika potongan per menit = Rp {{ number_format($rule->potongan_per_menit_telat, 0, ',', '.') }},
                    karyawan yang terlambat 30 menit akan dipotong
                    <strong>Rp {{ number_format($rule->potongan_per_menit_telat * 30, 0, ',', '.') }}</strong>.
                </div>
            </div>

            {{-- Tombol simpan --}}
            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
