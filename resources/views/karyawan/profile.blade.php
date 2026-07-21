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
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Lihat dan perbarui informasi pribadi Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">

        {{-- =====================================================
             KOLOM KIRI: Informasi yang TIDAK bisa diubah karyawan
             Data ini hanya bisa diubah oleh Admin/HRD
             ===================================================== --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-hairline shadow-sm p-5">
                <h2 class="font-semibold text-ink mb-4">Informasi Pekerjaan</h2>
                <p class="text-xs text-ink-muted-48 mb-3">Data ini hanya bisa diubah oleh Admin/HRD.</p>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">NIK</dt>
                        {{-- NIK = Nomor Induk Karyawan, identitas unik setiap karyawan --}}
                        <dd class="font-mono font-medium text-ink">{{ $employee->nik }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">Nama</dt>
                        <dd class="font-medium text-ink">{{ $employee->nama }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">Jabatan</dt>
                        <dd class="font-medium text-ink">{{ $employee->jabatan }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">Departemen</dt>
                        <dd class="font-medium text-ink">{{ $employee->departemen }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">Status Kerja</dt>
                        <dd>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($employee->status_kerja === 'tetap') bg-blue-100 text-primary
                                @elseif($employee->status_kerja === 'kontrak') bg-purple-100 text-purple-700
                                @else bg-canvas-parchment text-ink-muted-80
                                @endif">
                                {{ ucfirst($employee->status_kerja) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">Gaji Pokok</dt>
                        {{-- gaji_pokok_format = accessor dari model Employee yang sudah format Rp --}}
                        <dd class="font-bold text-ink">{{ $employee->gaji_pokok_format }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">Tgl Masuk</dt>
                        <dd class="text-ink-muted-80">{{ $employee->tanggal_masuk->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">Email Login</dt>
                        <dd class="text-ink-muted-80 text-xs">{{ auth()->user()->email }}</dd>
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
            <div class="bg-white rounded-xl border border-hairline shadow-sm p-6">
                <h2 class="font-semibold text-ink mb-4">Data Pribadi</h2>
                <p class="text-xs text-ink-muted-48 mb-4">
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
                            <label class="form-label">Nama Bank</label>
                            {{-- Dropdown bank: karyawan tinggal pilih dari daftar, tidak perlu ketik manual.
                                 old('nama_bank', $employee->nama_bank) → jika validasi gagal, tetap pilih yang tadi dipilih. --}}
                            <select name="nama_bank"
                                class="form-input @error('nama_bank') border-red-400 @enderror">
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
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" name="no_rekening"
                                value="{{ old('no_rekening', $employee->no_rekening) }}"
                                placeholder="Masukkan nomor rekening"
                                class="form-input @error('no_rekening') border-red-400 @enderror">
                            @error('no_rekening')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon"
                            value="{{ old('no_telepon', $employee->no_telepon) }}"
                            placeholder="cth: 08123456789"
                            class="form-input @error('no_telepon') border-red-400 @enderror">
                        @error('no_telepon')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" rows="3"
                            placeholder="Masukkan alamat lengkap"
                            class="form-input @error('alamat') border-red-400 @enderror">{{ old('alamat', $employee->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info: rekening penting untuk transfer gaji --}}
            <div class="mt-4 bg-primary/5 border border-primary/20 rounded-lg px-4 py-3 text-sm text-primary">
                <strong>ℹ️ Penting:</strong>
                Pastikan nama bank dan nomor rekening Anda sudah benar. Data ini digunakan untuk proses transfer gaji setiap bulan.
            </div>
        </div>
    </div>
@endsection
