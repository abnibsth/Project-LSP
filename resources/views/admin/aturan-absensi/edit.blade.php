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

        Layout:
        1. Header
        2. Ringkasan aturan aktif (read-only snapshot)
        3. Form 2 kolom: Jam Kerja | Potongan Gaji
        4. Catatan cara kerja + tombol simpan
    --}}

    @php
        // Format jam HH:MM dari DB (bisa "07:30:00")
        $jamMasuk = old('jam_masuk', \Illuminate\Support\Str::substr((string) $rule->jam_masuk, 0, 5));
        $jamKeluar = old('jam_keluar', \Illuminate\Support\Str::substr((string) $rule->jam_keluar, 0, 5));
        $toleransi = (int) old('toleransi_menit', $rule->toleransi_menit);
        $potonganAlpha = (int) old('potongan_per_alpha', $rule->potongan_per_alpha);
        $potonganMenit = (int) old('potongan_per_menit_telat', $rule->potongan_per_menit_telat);
        // Contoh dampak: telat 30 menit pakai aturan saat ini
        $contohTelat30 = $potonganMenit * 30;
    @endphp

    <div class="page-header">
        <h1 class="page-title">Aturan Absensi</h1>
        <p class="page-subtitle">
            Atur jam kerja, toleransi keterlambatan, dan nominal potongan untuk alpha &amp; telat.
            Perubahan berlaku ke seluruh karyawan.
        </p>
    </div>

    {{-- =====================================================
         RINGKASAN ATURAN AKTIF
         Snapshot biar HRD cepat cek tanpa baca form
         ===================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="ui-card ui-card-pad">
            <p class="text-xs text-ink-muted-48 mb-1">Jam Masuk</p>
            <p class="text-xl font-semibold text-ink tabular-nums tracking-tight">{{ $jamMasuk }}</p>
        </div>
        <div class="ui-card ui-card-pad">
            <p class="text-xs text-ink-muted-48 mb-1">Jam Keluar</p>
            <p class="text-xl font-semibold text-ink tabular-nums tracking-tight">{{ $jamKeluar }}</p>
        </div>
        <div class="ui-card ui-card-pad">
            <p class="text-xs text-ink-muted-48 mb-1">Toleransi</p>
            <p class="text-xl font-semibold text-ink tabular-nums tracking-tight">{{ $toleransi }} <span class="text-sm font-medium text-ink-muted-48">menit</span></p>
        </div>
        <div class="ui-card ui-card-pad">
            <p class="text-xs text-ink-muted-48 mb-1">Potongan Alpha / hari</p>
            <p class="text-xl font-semibold text-ink tabular-nums tracking-tight">
                Rp {{ number_format($potonganAlpha, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.aturan-absensi.update') }}">
        {{-- @csrf: Token keamanan wajib ada di setiap form POST --}}
        @csrf
        {{-- @method('PUT'): HTML form hanya bisa GET/POST, ini trick untuk kirim method PUT --}}
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
            {{-- =====================================================
                 BAGIAN 1: PENGATURAN JAM KERJA
                 Menentukan kapan karyawan dianggap hadir/telat
                 ===================================================== --}}
            <div class="xl:col-span-7 space-y-5">
                <div class="ui-card ui-card-pad">
                    <div class="flex items-start gap-3 mb-5">
                        <div class="stat-icon">
                            <i data-lucide="clock-3" class="ui-icon" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="section-title">Jam Kerja</h2>
                            <p class="text-xs text-ink-muted-48 mt-0.5">
                                Dipakai saat check-in untuk status HADIR atau TELAT.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="jam_masuk">Jam Masuk</label>
                            {{-- old() + nilai DB: kalau validasi gagal, input lama tetap tampil --}}
                            <input type="time" id="jam_masuk" name="jam_masuk"
                                value="{{ $jamMasuk }}"
                                class="form-input {{ $errors->has('jam_masuk') ? 'is-invalid' : '' }}">
                            @error('jam_masuk')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Batas ideal karyawan check-in.</p>
                        </div>

                        <div>
                            <label class="form-label" for="jam_keluar">Jam Keluar</label>
                            <input type="time" id="jam_keluar" name="jam_keluar"
                                value="{{ $jamKeluar }}"
                                class="form-input {{ $errors->has('jam_keluar') ? 'is-invalid' : '' }}">
                            @error('jam_keluar')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Referensi jam pulang standar perusahaan.</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label" for="toleransi_menit">Toleransi Keterlambatan</label>
                            {{-- Input Group: input + teks addon di kanan (menit) --}}
                            <div class="form-addon max-w-xs">
                                <input type="number" id="toleransi_menit" name="toleransi_menit" min="0" max="120"
                                    value="{{ $toleransi }}"
                                    class="form-input {{ $errors->has('toleransi_menit') ? 'is-invalid' : '' }}">
                                <span class="form-addon-suffix">menit</span>
                            </div>
                            @error('toleransi_menit')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            {{-- Penjelasan: jika check-in dalam toleransi → HADIR, lewat → TELAT --}}
                            <p class="form-hint">
                                Check-in sampai {{ $jamMasuk }} + {{ $toleransi }} menit masih dihitung
                                <strong class="text-ink">HADIR</strong>. Lewat dari itu = <strong class="text-ink">TELAT</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- =====================================================
                     BAGIAN 2: PENGATURAN POTONGAN GAJI
                     Menentukan berapa rupiah yang dipotong per kejadian
                     ===================================================== --}}
                <div class="ui-card ui-card-pad">
                    <div class="flex items-start gap-3 mb-5">
                        <div class="stat-icon">
                            <i data-lucide="wallet" class="ui-icon" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="section-title">Potongan Gaji</h2>
                            <p class="text-xs text-ink-muted-48 mt-0.5">
                                Dipakai saat proses payroll menghitung potongan absensi.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="potongan_per_alpha">Potongan per Hari Alpha</label>
                            {{-- Input Group: prefix 'Rp' di kiri + input angka --}}
                            <div class="form-addon">
                                <span class="form-addon-prefix">Rp</span>
                                <input type="number" id="potongan_per_alpha" name="potongan_per_alpha" min="0" step="1000"
                                    value="{{ $potonganAlpha }}"
                                    class="form-input {{ $errors->has('potongan_per_alpha') ? 'is-invalid' : '' }}">
                            </div>
                            @error('potongan_per_alpha')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Flat per hari karyawan tidak masuk (alpha).</p>
                        </div>

                        <div>
                            <label class="form-label" for="potongan_per_menit_telat">Potongan per Menit Telat</label>
                            <div class="form-addon">
                                <span class="form-addon-prefix">Rp</span>
                                <input type="number" id="potongan_per_menit_telat" name="potongan_per_menit_telat" min="0" step="100"
                                    value="{{ $potonganMenit }}"
                                    class="form-input {{ $errors->has('potongan_per_menit_telat') ? 'is-invalid' : '' }}">
                            </div>
                            @error('potongan_per_menit_telat')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Dikalikan total menit terlambat di periode payroll.</p>
                        </div>
                    </div>

                    {{-- Contoh perhitungan supaya admin paham dampak pengaturan --}}
                    <div class="alert alert-warning mt-5 mb-0">
                        <span class="alert-icon" aria-hidden="true">!</span>
                        <span>
                            Contoh dengan aturan saat ini:
                            potongan per menit
                            <strong>Rp {{ number_format($potonganMenit, 0, ',', '.') }}</strong>,
                            telat 30 menit ≈
                            <strong>Rp {{ number_format($contohTelat30, 0, ',', '.') }}</strong>.
                            Alpha 1 hari ≈
                            <strong>Rp {{ number_format($potonganAlpha, 0, ',', '.') }}</strong>.
                        </span>
                    </div>
                </div>

                {{-- Footer aksi form --}}
                <div class="ui-card ui-card-pad">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <p class="text-xs text-ink-muted-48 max-w-md">
                            Aturan ini global. Slip gaji yang sudah final tidak berubah otomatis
                            — hanya perhitungan absensi &amp; payroll berikutnya.
                        </p>
                        <button type="submit" class="btn btn-primary self-start sm:self-auto shrink-0">
                            <i data-lucide="save" class="ui-icon ui-icon-sm" aria-hidden="true"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>

            {{-- =====================================================
                 KOLOM KANAN: Cara kerja (bantuan HRD)
                 ===================================================== --}}
            <div class="xl:col-span-5 space-y-4">
                <div class="ui-card ui-card-pad">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="stat-icon">
                            <i data-lucide="list-checks" class="ui-icon" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Cara Sistem Membaca Aturan</h2>
                            <p class="text-xs text-ink-muted-48 mt-0.5">Ringkas, biar tidak salah set.</p>
                        </div>
                    </div>

                    <ol class="space-y-3 text-sm text-ink-muted-80">
                        <li class="flex gap-3">
                            <span class="badge badge-muted shrink-0 w-6 h-6 !p-0 inline-flex items-center justify-center">1</span>
                            <span>
                                Karyawan check-in lewat menu Absensi.
                                Sistem bandingkan waktu check-in dengan
                                <strong class="text-ink">Jam Masuk + Toleransi</strong>.
                            </span>
                        </li>
                        <li class="flex gap-3">
                            <span class="badge badge-muted shrink-0 w-6 h-6 !p-0 inline-flex items-center justify-center">2</span>
                            <span>
                                Dalam toleransi → status <strong class="text-ink">HADIR</strong>.
                                Lewat toleransi → <strong class="text-ink">TELAT</strong>
                                (menit telat dihitung).
                            </span>
                        </li>
                        <li class="flex gap-3">
                            <span class="badge badge-muted shrink-0 w-6 h-6 !p-0 inline-flex items-center justify-center">3</span>
                            <span>
                                Hari kerja tanpa absensi → <strong class="text-ink">ALPHA</strong>
                                saat rekap/payroll.
                            </span>
                        </li>
                        <li class="flex gap-3">
                            <span class="badge badge-muted shrink-0 w-6 h-6 !p-0 inline-flex items-center justify-center">4</span>
                            <span>
                                Saat <strong class="text-ink">Proses Payroll</strong>, potongan dihitung dari
                                alpha × potongan harian + menit telat × potongan per menit.
                            </span>
                        </li>
                    </ol>
                </div>

                <div class="ui-card ui-card-pad">
                    <p class="section-label mb-3">Ringkasan Dampak</p>
                    <dl class="info-list">
                        <div class="info-row">
                            <dt>Batas HADIR</dt>
                            <dd class="tabular-nums">{{ $jamMasuk }} + {{ $toleransi }} mnt</dd>
                        </div>
                        <div class="info-row">
                            <dt>Telat 15 menit</dt>
                            <dd class="tabular-nums">Rp {{ number_format($potonganMenit * 15, 0, ',', '.') }}</dd>
                        </div>
                        <div class="info-row">
                            <dt>Telat 30 menit</dt>
                            <dd class="tabular-nums">Rp {{ number_format($contohTelat30, 0, ',', '.') }}</dd>
                        </div>
                        <div class="info-row">
                            <dt>Alpha 1 hari</dt>
                            <dd class="tabular-nums">Rp {{ number_format($potonganAlpha, 0, ',', '.') }}</dd>
                        </div>
                        <div class="info-row">
                            <dt>Alpha 2 hari</dt>
                            <dd class="tabular-nums">Rp {{ number_format($potonganAlpha * 2, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="alert alert-info mb-0">
                    <span class="alert-icon" aria-hidden="true">i</span>
                    <span>
                        Hanya ada <strong>satu</strong> aturan aktif untuk seluruh PT Nikel Indonesia.
                        Cek dulu dampak di panel ringkasan sebelum menyimpan.
                    </span>
                </div>
            </div>
        </div>
    </form>
@endsection
