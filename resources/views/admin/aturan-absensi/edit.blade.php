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
        <h1 class="page-title">Aturan Absensi</h1>
        <p class="page-subtitle">
            Pengaturan jam kerja dan nominal potongan untuk keterlambatan & alpha.
        </p>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.aturan-absensi.update') }}" class="space-y-5">
            {{-- @csrf: Token keamanan wajib ada di setiap form POST --}}
            @csrf
            {{-- @method('PUT'): HTML form hanya bisa GET/POST, ini trick untuk kirim method PUT --}}
            @method('PUT')

            {{-- =====================================================
                 BAGIAN 1: PENGATURAN JAM KERJA
                 Menentukan kapan karyawan dianggap hadir/telat
                 ===================================================== --}}
            <div class="ui-card ui-card-pad">
                <h2 class="section-title mb-4">Jam Kerja</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label" for="jam_masuk">Jam Masuk</label>
                        {{-- old('jam_masuk', $rule->jam_masuk): tampilkan input lama jika form error,
                             atau nilai dari database jika pertama kali buka --}}
                        <input type="time" id="jam_masuk" name="jam_masuk"
                            value="{{ old('jam_masuk', \Illuminate\Support\Str::substr($rule->jam_masuk, 0, 5)) }}"
                            class="form-input {{ $errors->has('jam_masuk') ? 'is-invalid' : '' }}">
                        @error('jam_masuk')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="jam_keluar">Jam Keluar</label>
                        <input type="time" id="jam_keluar" name="jam_keluar"
                            value="{{ old('jam_keluar', \Illuminate\Support\Str::substr($rule->jam_keluar, 0, 5)) }}"
                            class="form-input {{ $errors->has('jam_keluar') ? 'is-invalid' : '' }}">
                        @error('jam_keluar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="toleransi_menit">Toleransi Keterlambatan</label>
                        {{-- Input Group: input + teks addon di kanan (menit) --}}
                        <div class="form-addon">
                            <input type="number" id="toleransi_menit" name="toleransi_menit" min="0" max="120"
                                value="{{ old('toleransi_menit', $rule->toleransi_menit) }}"
                                class="form-input {{ $errors->has('toleransi_menit') ? 'is-invalid' : '' }}">
                            <span class="form-addon-suffix">menit</span>
                        </div>
                        @error('toleransi_menit')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        {{-- Penjelasan: jika check-in dalam toleransi → HADIR, lewat → TELAT --}}
                        <p class="form-hint">
                            Check-in dalam {{ $rule->toleransi_menit }} menit setelah jam masuk = HADIR
                        </p>
                    </div>
                </div>
            </div>

            {{-- =====================================================
                 BAGIAN 2: PENGATURAN POTONGAN GAJI
                 Menentukan berapa rupiah yang dipotong per kejadian
                 ===================================================== --}}
            <div class="bg-white rounded-xl border border-hairline shadow-sm p-6">
                <h2 class="font-semibold text-ink mb-4 flex items-center gap-2">
                    ✂️ Potongan Gaji
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label" for="potongan_per_alpha">Potongan per Hari Alpha</label>
                        {{-- Input Group: prefix 'Rp' di kiri + input angka --}}
                        <div class="form-addon">
                            <span class="form-addon-prefix">Rp</span>
                            <input type="number" id="potongan_per_alpha" name="potongan_per_alpha" min="0" step="1000"
                                value="{{ (int) old('potongan_per_alpha', $rule->potongan_per_alpha) }}"
                                class="form-input {{ $errors->has('potongan_per_alpha') ? 'is-invalid' : '' }}">
                        </div>
                        @error('potongan_per_alpha')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">Dipotong flat per hari karyawan tidak masuk (alpha)</p>
                    </div>

                    <div>
                        <label class="form-label" for="potongan_per_menit_telat">Potongan per Menit Telat</label>
                        {{-- Input Group: prefix 'Rp' di kiri + input angka --}}
                        <div class="form-addon">
                            <span class="form-addon-prefix">Rp</span>
                            <input type="number" id="potongan_per_menit_telat" name="potongan_per_menit_telat" min="0" step="100"
                                value="{{ (int) old('potongan_per_menit_telat', $rule->potongan_per_menit_telat) }}"
                                class="form-input {{ $errors->has('potongan_per_menit_telat') ? 'is-invalid' : '' }}">
                        </div>
                        @error('potongan_per_menit_telat')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">Dipotong per menit × total menit terlambat</p>
                    </div>
                </div>

                {{-- Contoh perhitungan supaya admin paham dampak pengaturan --}}
                <div class="alert alert-warning mt-4 mb-0">
                    <span class="alert-icon" aria-hidden="true">!</span>
                    <span>
                        Contoh: potongan per menit Rp {{ number_format($rule->potongan_per_menit_telat, 0, ',', '.') }},
                        telat 30 menit = <strong>Rp {{ number_format($rule->potongan_per_menit_telat * 30, 0, ',', '.') }}</strong>.
                    </span>
                </div>
            </div>

            {{-- Tombol simpan --}}
            <div>
                <button type="submit" class="btn btn-primary">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
