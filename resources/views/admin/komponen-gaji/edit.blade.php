@extends('layouts.app')

@section('title', 'Edit Komponen Gaji')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.komponen-gaji.index') }}" class="text-sm text-ink-muted-48 hover:text-ink-muted-80">
            ← Kembali ke Komponen Gaji
        </a>
        <h1 class="page-title mt-2">Edit Komponen Gaji</h1>
    </div>

    <div class="bg-white rounded-xl border border-hairline shadow-sm p-6 max-w-lg">
        {{-- action ke route UPDATE dengan method PUT --}}
        <form method="POST" action="{{ route('admin.komponen-gaji.update', $salaryComponent) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">
                    Nama Komponen <span class="text-red-500">*</span>
                </label>
                {{-- old() fallback ke nilai dari database ($salaryComponent->nama_komponen) --}}
                <input type="text" name="nama_komponen" value="{{ old('nama_komponen', $salaryComponent->nama_komponen) }}"
                    class="form-input @error('nama_komponen') border-red-400 @enderror">
                @error('nama_komponen')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Tipe <span class="text-red-500">*</span></label>
                    <select name="tipe" required
                        class="form-input">
                        <option value="tunjangan" {{ old('tipe', $salaryComponent->tipe) === 'tunjangan' ? 'selected' : '' }}>Tunjangan (+)</option>
                        <option value="potongan"  {{ old('tipe', $salaryComponent->tipe) === 'potongan'  ? 'selected' : '' }}>Potongan (-)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jenis Nilai <span class="text-red-500">*</span></label>
                    <select name="jenis_nilai" required
                        class="form-input">
                        <option value="nominal"    {{ old('jenis_nilai', $salaryComponent->jenis_nilai) === 'nominal'    ? 'selected' : '' }}>Nominal (Rp)</option>
                        <option value="persentase" {{ old('jenis_nilai', $salaryComponent->jenis_nilai) === 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Nilai <span class="text-red-500">*</span></label>
                <input type="number" name="nilai" value="{{ old('nilai', $salaryComponent->nilai) }}" min="0" step="any"
                    class="form-input">
            </div>

            <div>
                <label class="form-label">Status</label>
                <label class="flex items-center gap-2 cursor-pointer">
                    {{-- Checkbox: jika dicentang → is_aktif = true, jika tidak → tidak terkirim (default false di controller) --}}
                    <input type="checkbox" name="is_aktif" value="1"
                        {{ old('is_aktif', $salaryComponent->is_aktif) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary border-hairline rounded focus:ring-primary">
                    <span class="text-sm text-ink-muted-80">Aktif (akan digunakan saat proses payroll)</span>
                </label>
            </div>

            <div>
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" rows="2"
                    class="form-input">{{ old('keterangan', $salaryComponent->keterangan) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="btn btn-primary">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.komponen-gaji.index') }}"
                    class="text-ink-muted-80 hover:text-ink px-6 py-2.5 rounded-lg text-sm border border-hairline">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
