@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Form tambah karyawan baru.
        Saat disimpan, sistem juga membuat User login (role karyawan)
        dengan email yang diisi dan password default: password.
    --}}

    <div class="page-header">
        <a href="{{ route('admin.karyawan.index') }}" class="back-link">← Kembali ke Daftar Karyawan</a>
        <h1 class="page-title">Tambah Karyawan Baru</h1>
        <p class="page-subtitle">Sistem membuat akun login otomatis dari email yang diisi.</p>
    </div>

    <div class="ui-card ui-card-pad max-w-2xl">
        <form method="POST" action="{{ route('admin.karyawan.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="nik">NIK (16 Digit KTP) <span class="text-red-500">*</span></label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required placeholder="3171012345670001"
                        class="form-input {{ $errors->has('nik') ? 'is-invalid' : '' }}">
                    @error('nik')<p class="form-error">{{ $message }}</p>@enderror
                    <p class="form-hint">Harus diawali <strong>317</strong> (DKI Jakarta) & 16 digit angka.</p>
                </div>
                <div>
                    <label class="form-label" for="nama">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                        class="form-input {{ $errors->has('nama') ? 'is-invalid' : '' }}">
                    @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label" for="email">Email (untuk Login) <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
                <p class="form-hint">Password default: <strong>password</strong></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="jabatan">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required class="form-input">
                </div>
                <div>
                    <label class="form-label" for="departemen">Departemen <span class="text-red-500">*</span></label>
                    <input type="text" id="departemen" name="departemen" value="{{ old('departemen') }}" required class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="status_kerja">Status Kerja <span class="text-red-500">*</span></label>
                    <select id="status_kerja" name="status_kerja" required class="form-select">
                        <option value="probation" {{ old('status_kerja') === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="kontrak" {{ old('status_kerja') === 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                        <option value="tetap" {{ old('status_kerja') === 'tetap' ? 'selected' : '' }}>Tetap</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="gaji_pokok">Gaji Pokok (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok', 0) }}" required min="0" step="50000"
                        class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="nama_bank">Nama Bank</label>
                    <select id="nama_bank" name="nama_bank" class="form-select">
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
                            <option value="{{ $bank }}" {{ old('nama_bank') === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="no_rekening">Nomor Rekening</label>
                    <input type="text" id="no_rekening" name="no_rekening" value="{{ old('no_rekening') }}" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="no_telepon">No. Telepon</label>
                    <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="tanggal_masuk">Tanggal Masuk <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required
                        class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label" for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2" class="form-textarea">{{ old('alamat') }}</textarea>
            </div>

            <div class="flex flex-wrap gap-2.5 pt-2">
                <button type="submit" class="btn btn-primary">Simpan Karyawan</button>
                <a href="{{ route('admin.karyawan.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
