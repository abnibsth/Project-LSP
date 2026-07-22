@extends('layouts.app')

@section('title', 'Edit Komponen Gaji')

@section('content')
    <div class="page-header">
        <a href="{{ route('admin.komponen-gaji.index') }}" class="back-link">← Kembali ke Komponen Gaji</a>
        <h1 class="page-title">Edit Komponen Gaji</h1>
    </div>

    <div class="ui-card ui-card-pad max-w-lg">
        {{-- action ke route UPDATE dengan method PUT --}}
        <form method="POST" action="{{ route('admin.komponen-gaji.update', $salaryComponent) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label" for="nama_komponen">Nama Komponen <span class="text-red-500">*</span></label>
                {{-- old() fallback ke nilai dari database ($salaryComponent->nama_komponen) --}}
                <input type="text" id="nama_komponen" name="nama_komponen" value="{{ old('nama_komponen', $salaryComponent->nama_komponen) }}"
                    class="form-input {{ $errors->has('nama_komponen') ? 'is-invalid' : '' }}">
                @error('nama_komponen')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="tipe">Tipe <span class="text-red-500">*</span></label>
                    <select id="tipe" name="tipe" required class="form-select">
                        <option value="tunjangan" {{ old('tipe', $salaryComponent->tipe) === 'tunjangan' ? 'selected' : '' }}>Tunjangan (+)</option>
                        <option value="potongan" {{ old('tipe', $salaryComponent->tipe) === 'potongan' ? 'selected' : '' }}>Potongan (−)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="jenis_nilai">Jenis Nilai <span class="text-red-500">*</span></label>
                    <select id="jenis_nilai" name="jenis_nilai" required class="form-select">
                        <option value="nominal" {{ old('jenis_nilai', $salaryComponent->jenis_nilai) === 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                        <option value="persentase" {{ old('jenis_nilai', $salaryComponent->jenis_nilai) === 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label" for="nilai">Nilai <span class="text-red-500">*</span></label>
                <input type="number" id="nilai" name="nilai" value="{{ old('nilai', $salaryComponent->nilai) }}" min="0" step="any"
                    class="form-input">
            </div>

            <div>
                <label class="form-label">Status</label>
                <label class="flex items-center gap-2 cursor-pointer">
                    {{-- Checkbox: jika dicentang → is_aktif = true, jika tidak → tidak terkirim (default false di controller) --}}
                    <input type="checkbox" name="is_aktif" value="1"
                        {{ old('is_aktif', $salaryComponent->is_aktif) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary border-hairline rounded focus:ring-primary">
                    <span class="text-sm text-ink-muted-80">Aktif (dipakai saat proses payroll)</span>
                </label>
            </div>

            <div>
                <label class="form-label" for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="2"
                    class="form-textarea">{{ old('keterangan', $salaryComponent->keterangan) }}</textarea>
            </div>

            <div class="flex flex-wrap gap-2.5 pt-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.komponen-gaji.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
