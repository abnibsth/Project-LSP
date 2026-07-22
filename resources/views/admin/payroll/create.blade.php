@extends('layouts.app')

@section('title', 'Buat Periode Payroll')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Membuat periode payroll (bulan + tahun).
        Setelah dibuat, admin masih harus klik Proses Payroll
        di halaman daftar agar gaji dihitung otomatis.
    --}}

    <div class="page-header">
        <a href="{{ route('admin.payroll.index') }}" class="back-link">← Kembali ke Payroll</a>
        <h1 class="page-title">Buat Periode Payroll Baru</h1>
    </div>

    <div class="ui-card ui-card-pad max-w-md">
        <form method="POST" action="{{ route('admin.payroll.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="form-label" for="bulan">Bulan</label>
                <select id="bulan" name="bulan" required
                    class="form-select {{ $errors->has('bulan') ? 'is-invalid' : '' }}">
                    <option value="">Pilih Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ old('bulan') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                @error('bulan')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label" for="tahun">Tahun</label>
                <select id="tahun" name="tahun" required class="form-select">
                    @foreach(range(date('Y'), date('Y') - 3) as $y)
                        <option value="{{ $y }}" {{ old('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="alert alert-info mb-0">
                <span class="alert-icon" aria-hidden="true">i</span>
                <span>
                    Setelah periode dibuat, klik <strong>Proses Payroll</strong> untuk menghitung gaji karyawan aktif.
                </span>
            </div>

            <div class="flex flex-wrap gap-2.5 pt-1">
                <button type="submit" class="btn btn-primary">Buat Periode</button>
                <a href="{{ route('admin.payroll.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
