@extends('layouts.app')

@section('title', 'Edit Komponen Gaji')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.komponen-gaji.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Kembali ke Komponen Gaji
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Komponen Gaji</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-lg">
        {{-- action ke route UPDATE dengan method PUT --}}
        <form method="POST" action="{{ route('admin.komponen-gaji.update', $salaryComponent) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Komponen <span class="text-red-500">*</span>
                </label>
                {{-- old() fallback ke nilai dari database ($salaryComponent->nama_komponen) --}}
                <input type="text" name="nama_komponen" value="{{ old('nama_komponen', $salaryComponent->nama_komponen) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('nama_komponen') border-red-400 @enderror">
                @error('nama_komponen')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                    <select name="tipe" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="tunjangan" {{ old('tipe', $salaryComponent->tipe) === 'tunjangan' ? 'selected' : '' }}>Tunjangan (+)</option>
                        <option value="potongan"  {{ old('tipe', $salaryComponent->tipe) === 'potongan'  ? 'selected' : '' }}>Potongan (-)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Nilai <span class="text-red-500">*</span></label>
                    <select name="jenis_nilai" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="nominal"    {{ old('jenis_nilai', $salaryComponent->jenis_nilai) === 'nominal'    ? 'selected' : '' }}>Nominal (Rp)</option>
                        <option value="persentase" {{ old('jenis_nilai', $salaryComponent->jenis_nilai) === 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai <span class="text-red-500">*</span></label>
                <input type="number" name="nilai" value="{{ old('nilai', $salaryComponent->nilai) }}" min="0" step="any"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <label class="flex items-center gap-2 cursor-pointer">
                    {{-- Checkbox: jika dicentang → is_aktif = true, jika tidak → tidak terkirim (default false di controller) --}}
                    <input type="checkbox" name="is_aktif" value="1"
                        {{ old('is_aktif', $salaryComponent->is_aktif) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Aktif (akan digunakan saat proses payroll)</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $salaryComponent->keterangan) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.komponen-gaji.index') }}"
                    class="text-gray-600 hover:text-gray-800 px-6 py-2.5 rounded-lg text-sm border border-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
