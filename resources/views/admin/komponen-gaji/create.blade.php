@extends('layouts.app')

@section('title', 'Tambah Komponen Gaji')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Form untuk menambah komponen gaji baru (tunjangan atau potongan).
        Komponen ini nantinya dipakai otomatis saat proses payroll dijalankan.
    --}}

    <div class="page-header">
        <a href="{{ route('admin.komponen-gaji.index') }}" class="back-link">← Kembali ke Komponen Gaji</a>
        <h1 class="page-title">Tambah Komponen Gaji</h1>
    </div>

    <div class="ui-card ui-card-pad max-w-lg">
        <form method="POST" action="{{ route('admin.komponen-gaji.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="form-label" for="nama_komponen">Nama Komponen <span class="text-red-500">*</span></label>
                <input type="text" id="nama_komponen" name="nama_komponen" value="{{ old('nama_komponen') }}"
                    placeholder="cth: Tunjangan Makan, Kasbon"
                    class="form-input {{ $errors->has('nama_komponen') ? 'is-invalid' : '' }}">
                @error('nama_komponen')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="tipe">Tipe <span class="text-red-500">*</span></label>
                    {{-- Tipe menentukan apakah komponen ini menambah (tunjangan) atau mengurangi (potongan) gaji --}}
                    <select id="tipe" name="tipe" required class="form-select">
                        <option value="tunjangan" {{ old('tipe') === 'tunjangan' ? 'selected' : '' }}>Tunjangan (+)</option>
                        <option value="potongan" {{ old('tipe') === 'potongan' ? 'selected' : '' }}>Potongan (−)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="jenis_nilai">Jenis Nilai <span class="text-red-500">*</span></label>
                    {{-- Nominal = angka tetap rupiah, Persentase = % dari gaji pokok --}}
                    <select id="jenis_nilai" name="jenis_nilai" required class="form-select">
                        <option value="nominal" {{ old('jenis_nilai') === 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                        <option value="persentase" {{ old('jenis_nilai') === 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label" for="nilai">Nilai <span class="text-red-500">*</span></label>
                <input type="number" id="nilai" name="nilai" value="{{ old('nilai', 0) }}" min="0" step="any"
                    class="form-input {{ $errors->has('nilai') ? 'is-invalid' : '' }}">
                @error('nilai')<p class="form-error">{{ $message }}</p>@enderror
                <p class="form-hint">
                    Nominal: angka rupiah (mis. 500000). Persentase: angka persen (mis. 10).
                </p>
            </div>

            <div>
                <label class="form-label" for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="2" placeholder="Opsional"
                    class="form-textarea">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex flex-wrap gap-2.5 pt-2">
                <button type="submit" class="btn btn-primary">Simpan Komponen</button>
                <a href="{{ route('admin.komponen-gaji.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
