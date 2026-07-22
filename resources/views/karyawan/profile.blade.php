@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Karyawan bisa melihat dan mengubah data PRIBADI sendiri.
        Yang bisa diubah: no rekening, nama bank, alamat, no telepon.
        Yang TIDAK bisa diubah: NIK, nama, jabatan, departemen, gaji pokok
        (hanya Admin/HRD yang bisa mengubah data tersebut).
    --}}

    <div class="page-header">
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Lihat dan perbarui informasi pribadi Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 max-w-5xl">

        {{-- =====================================================
             KOLOM KIRI: Informasi yang TIDAK bisa diubah karyawan
             Data ini hanya bisa diubah oleh Admin/HRD
             ===================================================== --}}
        <div class="space-y-4">
            <div class="ui-card ui-card-pad">
                <h2 class="section-title mb-1">Informasi Pekerjaan</h2>
                <p class="text-xs text-ink-muted-48 mb-4">Hanya Admin/HRD yang dapat mengubah data ini.</p>
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
                        <dd class="font-semibold">{{ $employee->gaji_pokok_format }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Tgl Masuk</dt>
                        <dd class="!font-normal text-ink-muted-80">{{ $employee->tanggal_masuk->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Email Login</dt>
                        <dd class="!font-normal text-ink-muted-80 text-xs">{{ auth()->user()->email }}</dd>
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
        <div class="lg:col-span-2 space-y-4">
            <div class="ui-card ui-card-pad">
                <h2 class="section-title mb-1">Data Pribadi</h2>
                <p class="text-xs text-ink-muted-48 mb-5">
                    Pastikan data rekening benar agar transfer gaji lancar.
                </p>

                {{-- Form: action ke route update profil --}}
                <form method="POST" action="{{ route('karyawan.profil.update') }}" class="space-y-4">
                    {{-- @csrf: Token keamanan Laravel, wajib di setiap form POST --}}
                    @csrf
                    {{-- @method('PUT'): Karena HTML form hanya bisa GET/POST, ini
                         memberitahu Laravel bahwa sebenarnya ini PUT request --}}
                    @method('PUT')

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
                        <textarea id="alamat" name="alamat" rows="3"
                            placeholder="Masukkan alamat lengkap"
                            class="form-textarea {{ $errors->has('alamat') ? 'is-invalid' : '' }}">{{ old('alamat', $employee->alamat) }}</textarea>
                        @error('alamat')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="btn btn-primary">
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
                </span>
            </div>
        </div>
    </div>
@endsection
