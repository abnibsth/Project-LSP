@extends('layouts.app')

@section('title', 'Tambah Komponen Gaji')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Form untuk menambah komponen gaji baru (tunjangan atau potongan).
        Komponen ini nantinya dipakai otomatis saat proses payroll dijalankan.
    --}}

    <div class="mb-6">
        <a href="{{ route('admin.komponen-gaji.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Kembali ke Komponen Gaji
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Komponen Gaji</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-lg">
        <form method="POST" action="{{ route('admin.komponen-gaji.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Komponen <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_komponen" value="{{ old('nama_komponen') }}"
                    placeholder="cth: Tunjangan Makan, Kasbon"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('nama_komponen') border-red-400 @enderror">
                @error('nama_komponen')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    {{-- Tipe menentukan apakah komponen ini menambah (tunjangan) atau mengurangi (potongan) gaji --}}
                    <select name="tipe" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="tunjangan" {{ old('tipe') === 'tunjangan' ? 'selected' : '' }}>Tunjangan (+)</option>
                        <option value="potongan"  {{ old('tipe') === 'potongan'  ? 'selected' : '' }}>Potongan (-)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis Nilai <span class="text-red-500">*</span>
                    </label>
                    {{-- Nominal = angka tetap rupiah, Persentase = % dari gaji pokok --}}
                    <select name="jenis_nilai" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="nominal"    {{ old('jenis_nilai') === 'nominal'    ? 'selected' : '' }}>Nominal (Rp)</option>
                        <option value="persentase" {{ old('jenis_nilai') === 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nilai <span class="text-red-500">*</span>
                </label>
                <input type="number" name="nilai" value="{{ old('nilai', 0) }}" min="0" step="any"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('nilai') border-red-400 @enderror">
                @error('nilai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">
                    Jika jenis = Nominal: isi angka rupiah (misal: 500000).
                    Jika jenis = Persentase: isi angka persen (misal: 10 untuk 10%).
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2" placeholder="Opsional..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                    Simpan Komponen
                </button>
                <a href="{{ route('admin.komponen-gaji.index') }}"
                    class="text-gray-600 hover:text-gray-800 px-6 py-2.5 rounded-lg text-sm border border-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
