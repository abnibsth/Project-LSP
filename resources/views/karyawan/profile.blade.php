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

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-gray-500 text-sm mt-1">Lihat dan perbarui informasi pribadi Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">

        {{-- =====================================================
             KOLOM KIRI: Informasi yang TIDAK bisa diubah karyawan
             Data ini hanya bisa diubah oleh Admin/HRD
             ===================================================== --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4">Informasi Pekerjaan</h2>
                <p class="text-xs text-gray-400 mb-3">Data ini hanya bisa diubah oleh Admin/HRD.</p>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">NIK</dt>
                        {{-- NIK = Nomor Induk Karyawan, identitas unik setiap karyawan --}}
                        <dd class="font-mono font-medium text-gray-900">{{ $employee->nik }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="font-medium text-gray-900">{{ $employee->nama }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Jabatan</dt>
                        <dd class="font-medium text-gray-900">{{ $employee->jabatan }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Departemen</dt>
                        <dd class="font-medium text-gray-900">{{ $employee->departemen }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status Kerja</dt>
                        <dd>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($employee->status_kerja === 'tetap') bg-blue-100 text-blue-700
                                @elseif($employee->status_kerja === 'kontrak') bg-purple-100 text-purple-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ ucfirst($employee->status_kerja) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Gaji Pokok</dt>
                        {{-- gaji_pokok_format = accessor dari model Employee yang sudah format Rp --}}
                        <dd class="font-bold text-gray-900">{{ $employee->gaji_pokok_format }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tgl Masuk</dt>
                        <dd class="text-gray-700">{{ $employee->tanggal_masuk->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email Login</dt>
                        <dd class="text-gray-700 text-xs">{{ auth()->user()->email }}</dd>
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
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Data Pribadi</h2>
                <p class="text-xs text-gray-400 mb-4">
                    Anda bisa memperbarui informasi berikut. Pastikan data rekening benar agar transfer gaji lancar.
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                            {{-- Dropdown bank: karyawan tinggal pilih dari daftar, tidak perlu ketik manual.
                                 old('nama_bank', $employee->nama_bank) → jika validasi gagal, tetap pilih yang tadi dipilih. --}}
                            <select name="nama_bank"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('nama_bank') border-red-400 @enderror">
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
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                            <input type="text" name="no_rekening"
                                value="{{ old('no_rekening', $employee->no_rekening) }}"
                                placeholder="Masukkan nomor rekening"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('no_rekening') border-red-400 @enderror">
                            @error('no_rekening')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input type="text" name="no_telepon"
                            value="{{ old('no_telepon', $employee->no_telepon) }}"
                            placeholder="cth: 08123456789"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('no_telepon') border-red-400 @enderror">
                        @error('no_telepon')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" rows="3"
                            placeholder="Masukkan alamat lengkap"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-400 @enderror">{{ old('alamat', $employee->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info: rekening penting untuk transfer gaji --}}
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
                <strong>ℹ️ Penting:</strong>
                Pastikan nama bank dan nomor rekening Anda sudah benar. Data ini digunakan untuk proses transfer gaji setiap bulan.
            </div>
        </div>
    </div>
@endsection
