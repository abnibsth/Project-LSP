@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.karyawan.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Daftar Karyawan</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Karyawan Baru</h1>
        <p class="text-gray-500 text-sm">Sistem akan otomatis membuat akun login dengan email yang diisi.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.karyawan.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK (16 Digit KTP) <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" value="{{ old('nik') }}" required placeholder="3171012345670001"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 {{ $errors->has('nik') ? 'border-red-400' : '' }}">
                    @error('nik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Harus diawali <strong>317</strong> (DKI Jakarta) & 16 digit angka.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email (untuk Login) <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-400' : '' }}">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">Password default: <strong>password</strong> (karyawan bisa ganti)</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departemen <span class="text-red-500">*</span></label>
                    <input type="text" name="departemen" value="{{ old('departemen') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Kerja <span class="text-red-500">*</span></label>
                    <select name="status_kerja" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="probation" {{ old('status_kerja') === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="kontrak" {{ old('status_kerja') === 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                        <option value="tetap" {{ old('status_kerja') === 'tetap' ? 'selected' : '' }}>Tetap</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gaji Pokok (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="gaji_pokok" value="{{ old('gaji_pokok', 0) }}" required min="0" step="50000"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                    <select name="nama_bank"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="no_rekening" value="{{ old('no_rekening') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">{{ old('alamat') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm">
                    Simpan Karyawan
                </button>
                <a href="{{ route('admin.karyawan.index') }}"
                    class="text-gray-600 hover:text-gray-800 px-6 py-2.5 rounded-lg text-sm border border-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
