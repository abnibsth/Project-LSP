@extends('layouts.app')

@section('title', 'Buat Periode Payroll')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.payroll.index') }}" class="text-sm text-ink-muted-48 hover:text-ink-muted-80">← Kembali ke Payroll</a>
        <h1 class="page-title mt-2">Buat Periode Payroll Baru</h1>
    </div>

    <div class="max-w-md bg-white rounded-xl border border-hairline shadow-sm p-6">
        <form method="POST" action="{{ route('admin.payroll.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="form-label">Bulan</label>
                <select name="bulan" required
                    class="form-input {{ $errors->has('bulan') ? 'border-red-400' : '' }}">
                    <option value="">Pilih Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ old('bulan') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                @error('bulan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Tahun</label>
                <select name="tahun" required
                    class="form-input">
                    @foreach(range(date('Y'), date('Y') - 3) as $y)
                        <option value="{{ $y }}" {{ old('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-primary/5 border border-primary/20 rounded-lg p-3 text-sm text-primary">
                ℹ️ Setelah periode dibuat, klik <strong>Proses Payroll</strong> untuk menghitung gaji semua karyawan aktif secara otomatis.
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm">
                    Buat Periode
                </button>
                <a href="{{ route('admin.payroll.index') }}"
                    class="text-ink-muted-80 hover:text-ink px-5 py-2.5 rounded-lg text-sm border border-hairline">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
