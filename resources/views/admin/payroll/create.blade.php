@extends('layouts.app')

@section('title', 'Buat Periode Payroll')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.payroll.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Payroll</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Buat Periode Payroll Baru</h1>
    </div>

    <div class="max-w-md bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.payroll.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="bulan" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 {{ $errors->has('bulan') ? 'border-red-400' : '' }}">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach(range(date('Y'), date('Y') - 3) as $y)
                        <option value="{{ $y }}" {{ old('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">
                ℹ️ Setelah periode dibuat, klik <strong>Proses Payroll</strong> untuk menghitung gaji semua karyawan aktif secara otomatis.
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm">
                    Buat Periode
                </button>
                <a href="{{ route('admin.payroll.index') }}"
                    class="text-gray-600 hover:text-gray-800 px-5 py-2.5 rounded-lg text-sm border border-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
