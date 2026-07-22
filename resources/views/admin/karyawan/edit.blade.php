@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Form edit data karyawan yang sudah ada.
        Berbeda dengan form "Tambah":
        - Tidak ada field email (email tidak bisa diubah dari sini)
        - NIK pakai unique ignore (boleh sama dengan milik sendiri)
        - Semua field sudah terisi dengan data yang ada di database (old() atau $employee->xxx)
    --}}

    <div class="mb-6">
        <a href="{{ route('admin.karyawan.show', $employee) }}" class="text-sm text-ink-muted-48 hover:text-ink-muted-80">
            ← Kembali ke Detail {{ $employee->nama }}
        </a>
        <h1 class="page-title">Edit Karyawan</h1>
        <p class="page-subtitle">NIK: <span class="font-mono">{{ $employee->nik }}</span></p>
    </div>

    <div class="ui-card ui-card-pad max-w-2xl">
        {{-- action mengarah ke route UPDATE (PUT request) --}}
        <form method="POST" action="{{ route('admin.karyawan.update', $employee) }}" class="space-y-4">
            @csrf
            @method('PUT') {{-- Spoofing method: HTML form hanya bisa POST, ini mengubahnya jadi PUT --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="nik">NIK (16 Digit KTP) <span class="text-red-500">*</span></label>
                    {{-- old('nik', $employee->nik): pakai input lama jika ada error validasi, atau nilai dari DB --}}
                    <input type="text" id="nik" name="nik" value="{{ old('nik', $employee->nik) }}" required
                        class="form-input {{ $errors->has('nik') ? 'is-invalid' : '' }}">
                    @error('nik')<p class="form-error">{{ $message }}</p>@enderror
                    <p class="form-hint">Harus diawali <strong>317</strong> (DKI Jakarta) & 16 digit angka.</p>
                </div>
                <div>
                    <label class="form-label" for="nama">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $employee->nama) }}" required
                        class="form-input {{ $errors->has('nama') ? 'is-invalid' : '' }}">
                    @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Email tidak bisa diubah, hanya ditampilkan sebagai info --}}
            <div>
                <label class="form-label">Email Login</label>
                <input type="text" value="{{ $employee->user?->email ?? '-' }}" disabled
                    class="form-input bg-canvas-parchment text-ink-muted-48 cursor-not-allowed">
                <p class="form-hint">Email tidak bisa diubah dari sini.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="jabatan">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan', $employee->jabatan) }}" required class="form-input">
                </div>
                <div>
                    <label class="form-label" for="departemen">Departemen <span class="text-red-500">*</span></label>
                    <input type="text" id="departemen" name="departemen" value="{{ old('departemen', $employee->departemen) }}" required class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="status_kerja">Status Kerja <span class="text-red-500">*</span></label>
                    <select id="status_kerja" name="status_kerja" required class="form-select">
                        {{-- Untuk setiap option, cek apakah ini nilai yang sedang tersimpan --}}
                        <option value="probation" {{ old('status_kerja', $employee->status_kerja) === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="kontrak" {{ old('status_kerja', $employee->status_kerja) === 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                        <option value="tetap" {{ old('status_kerja', $employee->status_kerja) === 'tetap' ? 'selected' : '' }}>Tetap</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="gaji_pokok">Gaji Pokok (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok', $employee->gaji_pokok) }}" required min="0" step="50000"
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
                            <option value="{{ $bank }}" {{ old('nama_bank', $employee->nama_bank) === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="no_rekening">Nomor Rekening</label>
                    <input type="text" id="no_rekening" name="no_rekening" value="{{ old('no_rekening', $employee->no_rekening) }}" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="no_telepon">No. Telepon</label>
                    <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $employee->no_telepon) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="tanggal_masuk">Tanggal Masuk <span class="text-red-500">*</span></label>
                    {{-- format('Y-m-d') penting untuk input type="date" --}}
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', $employee->tanggal_masuk->format('Y-m-d')) }}" required
                        class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label" for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2" class="form-textarea">{{ old('alamat', $employee->alamat) }}</textarea>
            </div>

            <div class="flex flex-wrap gap-2.5 pt-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.karyawan.show', $employee) }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
