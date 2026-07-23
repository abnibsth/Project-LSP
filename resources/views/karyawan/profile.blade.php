@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Karyawan bisa melihat dan mengubah data PRIBADI sendiri.
        Yang bisa diubah: no rekening, nama bank, alamat, no telepon.
        Yang TIDAK bisa diubah: NIK, nama, jabatan, departemen, gaji pokok
        (hanya Admin/HRD yang bisa mengubah data tersebut).

        Layout:
        1. Banner profil (avatar + identitas ringkas)
        2. Grid 2 kolom full-width
           - Kiri  : info pekerjaan (read-only)
           - Kanan : form data pribadi (editable)
    --}}

    @if(!$employee)
        <div class="alert alert-warning">
            <span class="alert-icon" aria-hidden="true">!</span>
            <span>Data karyawan belum terhubung ke akun ini. Hubungi Admin/HRD.</span>
        </div>
    @else
        {{-- =====================================================
             BAGIAN 1: Banner profil
             Memberi konteks "siapa saya" sebelum form
             ===================================================== --}}
        <div class="profile-hero mb-5">
            <div class="profile-hero-avatar" aria-hidden="true">
                {{ strtoupper(mb_substr($employee->nama, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="profile-hero-kicker">Profil Saya</p>
                <h1 class="profile-hero-name">{{ $employee->nama }}</h1>
                <p class="profile-hero-meta">
                    {{ $employee->jabatan }}
                    <span class="profile-hero-dot" aria-hidden="true">·</span>
                    {{ $employee->departemen }}
                    <span class="profile-hero-dot" aria-hidden="true">·</span>
                    <span class="font-mono">{{ $employee->nik }}</span>
                </p>
            </div>
            <div class="profile-hero-side">
                <span class="badge
                    @if($employee->status_kerja === 'tetap') badge-primary
                    @elseif($employee->status_kerja === 'kontrak') badge-purple
                    @else badge-muted
                    @endif">
                    {{ ucfirst($employee->status_kerja) }}
                </span>
                <p class="text-xs text-ink-muted-48 mt-2">
                    Bergabung {{ $employee->tanggal_masuk?->translatedFormat('d F Y') ?? '—' }}
                </p>
            </div>
        </div>

        {{-- =====================================================
             BAGIAN 2: Konten utama full-width
             Hapus max-w sempit agar tidak terasa mentok kiri
             ===================================================== --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">

            {{-- =====================================================
                 KOLOM KIRI: Informasi yang TIDAK bisa diubah karyawan
                 Data ini hanya bisa diubah oleh Admin/HRD
                 ===================================================== --}}
            <div class="xl:col-span-4 space-y-4">
                <div class="ui-card ui-card-pad">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="stat-icon">
                            <i data-lucide="briefcase" class="ui-icon" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="section-title">Informasi Pekerjaan</h2>
                            <p class="text-xs text-ink-muted-48 mt-0.5">
                                Hanya Admin/HRD yang dapat mengubah data ini.
                            </p>
                        </div>
                    </div>

                    <dl class="info-list">
                        <div class="info-row">
                            <dt>NIK</dt>
                            {{-- NIK = Nomor Induk Karyawan, identitas unik setiap karyawan --}}
                            <dd class="font-mono">{{ $employee->nik }}</dd>
                        </div>
                        <div class="info-row">
                            <dt>Nama</dt>
                            <dd>{{ $employee->nama }}</dd>
                        </div>
                        <div class="info-row">
                            <dt>Jabatan</dt>
                            <dd>{{ $employee->jabatan }}</dd>
                        </div>
                        <div class="info-row">
                            <dt>Departemen</dt>
                            <dd>{{ $employee->departemen }}</dd>
                        </div>
                        <div class="info-row">
                            <dt>Status Kerja</dt>
                            <dd>
                                <span class="badge
                                    @if($employee->status_kerja === 'tetap') badge-primary
                                    @elseif($employee->status_kerja === 'kontrak') badge-purple
                                    @else badge-muted
                                    @endif">
                                    {{ ucfirst($employee->status_kerja) }}
                                </span>
                            </dd>
                        </div>
                        <div class="info-row">
                            <dt>Gaji Pokok</dt>
                            {{-- gaji_pokok_format = accessor dari model Employee yang sudah format Rp --}}
                            <dd class="font-semibold tabular-nums">{{ $employee->gaji_pokok_format }}</dd>
                        </div>
                        <div class="info-row">
                            <dt>Tgl Masuk</dt>
                            <dd class="!font-normal text-ink-muted-80">
                                {{ $employee->tanggal_masuk?->translatedFormat('d M Y') ?? '—' }}
                            </dd>
                        </div>
                        <div class="info-row">
                            <dt>Email Login</dt>
                            <dd class="!font-normal text-ink-muted-80 text-xs break-all">
                                {{ auth()->user()->email }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Ringkasan rekening (read-only) biar cepat dicek tanpa scroll form --}}
                <div class="ui-card ui-card-pad">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="stat-icon">
                            <i data-lucide="landmark" class="ui-icon" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Rekening Transfer</h2>
                            <p class="text-xs text-ink-muted-48 mt-0.5">Dipakai untuk transfer gaji bulanan.</p>
                        </div>
                    </div>
                    <dl class="info-list">
                        <div class="info-row">
                            <dt>Bank</dt>
                            <dd class="!text-left text-right max-w-[60%]">
                                {{ $employee->nama_bank ?: '—' }}
                            </dd>
                        </div>
                        <div class="info-row">
                            <dt>No. Rekening</dt>
                            <dd class="font-mono">{{ $employee->no_rekening ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- =====================================================
                 KOLOM KANAN: Form update data pribadi
                 Karyawan bisa mengubah 4 field ini sendiri:
                 1. Nama Bank (untuk transfer gaji)
                 2. No Rekening (untuk transfer gaji)
                 3. No Telepon
                 4. Alamat
                 ===================================================== --}}
            <div class="xl:col-span-8 space-y-4">
                <div class="ui-card ui-card-pad">
                    <div class="flex items-start gap-3 mb-5">
                        <div class="stat-icon">
                            <i data-lucide="user-round-pen" class="ui-icon" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="section-title">Data Pribadi</h2>
                            <p class="text-xs text-ink-muted-48 mt-0.5">
                                Pastikan data rekening benar agar transfer gaji lancar.
                            </p>
                        </div>
                    </div>

                    {{-- Form: action ke route update profil --}}
                    <form method="POST" action="{{ route('karyawan.profil.update') }}" class="space-y-6">
                        {{-- @csrf: Token keamanan Laravel, wajib di setiap form POST --}}
                        @csrf
                        {{-- @method('PUT'): Karena HTML form hanya bisa GET/POST, ini
                             memberitahu Laravel bahwa sebenarnya ini PUT request --}}
                        @method('PUT')

                        {{-- Kelompok 1: data rekening (paling penting untuk payroll) --}}
                        <div>
                            <p class="section-label mb-3">Rekening Gaji</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label" for="nama_bank">Nama Bank</label>
                                    {{-- Dropdown bank: karyawan tinggal pilih dari daftar, tidak perlu ketik manual.
                                         old('nama_bank', $employee->nama_bank) → jika validasi gagal, tetap pilih yang tadi dipilih. --}}
                                    <select id="nama_bank" name="nama_bank"
                                        class="form-select {{ $errors->has('nama_bank') ? 'is-invalid' : '' }}">
                                        <option value="">— Pilih Bank —</option>
                                        @foreach([
                                            'Bank BCA (Bank Central Asia)',
                                            'Bank Mandiri',
                                            'Bank BRI (Bank Rakyat Indonesia)',
                                            'Bank BNI (Bank Negara Indonesia)',
                                            'Bank Syariah Indonesia (BSI)',
                                            'Bank CIMB Niaga',
                                            'Bank Danamon',
                                            'Bank Maybank Indonesia',
                                            'Bank Permata',
                                            'Bank BTN (Bank Tabungan Negara)',
                                        ] as $bank)
                                            {{-- Cek apakah bank ini yang sedang tersimpan di database → kalau iya, tandai selected --}}
                                            <option value="{{ $bank }}" {{ old('nama_bank', $employee->nama_bank) === $bank ? 'selected' : '' }}>
                                                {{ $bank }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nama_bank')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label" for="no_rekening">Nomor Rekening</label>
                                    <input type="text" id="no_rekening" name="no_rekening"
                                        value="{{ old('no_rekening', $employee->no_rekening) }}"
                                        placeholder="Masukkan nomor rekening"
                                        class="form-input {{ $errors->has('no_rekening') ? 'is-invalid' : '' }}">
                                    @error('no_rekening')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Kelompok 2: kontak & alamat --}}
                        <div>
                            <p class="section-label mb-3">Kontak & Alamat</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="form-label" for="no_telepon">No. Telepon</label>
                                    <input type="text" id="no_telepon" name="no_telepon"
                                        value="{{ old('no_telepon', $employee->no_telepon) }}"
                                        placeholder="cth: 08123456789"
                                        class="form-input {{ $errors->has('no_telepon') ? 'is-invalid' : '' }}">
                                    @error('no_telepon')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label" for="alamat">Alamat</label>
                                    <textarea id="alamat" name="alamat" rows="4"
                                        placeholder="Masukkan alamat lengkap"
                                        class="form-textarea {{ $errors->has('alamat') ? 'is-invalid' : '' }}">{{ old('alamat', $employee->alamat) }}</textarea>
                                    @error('alamat')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1 border-t border-divider-soft">
                            <p class="text-xs text-ink-muted-48">
                                Perubahan langsung tersimpan ke data karyawan Anda.
                            </p>
                            <button type="submit" class="btn btn-primary self-start sm:self-auto">
                                <i data-lucide="save" class="ui-icon ui-icon-sm" aria-hidden="true"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Info: rekening penting untuk transfer gaji --}}
                <div class="alert alert-info mb-0">
                    <span class="alert-icon" aria-hidden="true">i</span>
                    <span>
                        Nama bank dan nomor rekening digunakan untuk transfer gaji bulanan.
                        Pastikan nomor rekening aktif atas nama Anda.
                    </span>
                </div>
            </div>
        </div>
    @endif
@endsection
