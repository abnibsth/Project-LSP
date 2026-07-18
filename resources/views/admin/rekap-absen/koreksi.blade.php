@extends('layouts.app')

@section('title', 'Koreksi Absensi')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Admin bisa mengoreksi data absensi yang salah.
        Dipakai saat: karyawan lupa check-in, mesin absen error, atau ada keluhan.
        Setelah koreksi, kolom is_koreksi = true (menandai data sudah pernah diubah admin).
    --}}

    <div class="mb-6">
        <a href="{{ route('admin.absensi.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Kembali ke Rekap Absensi
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Koreksi Data Absensi</h1>
        <p class="text-gray-500 text-sm">
            {{ $attendance->employee->nama }} —
            {{ $attendance->tanggal->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- Info kondisi absensi sebelum dikoreksi --}}
    <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-700 mb-6 flex items-start gap-2">
        <span class="text-lg">⚠️</span>
        <div>
            <strong>Data saat ini:</strong>
            Status = <strong>{{ ucfirst($attendance->status) }}</strong>,
            Check-in = <strong>{{ $attendance->waktu_checkin?->format('H:i') ?? 'Belum check-in' }}</strong>,
            Check-out = <strong>{{ $attendance->waktu_checkout?->format('H:i') ?? 'Belum check-out' }}</strong>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-lg">
        {{-- action ke route UPDATE koreksi dengan method PUT --}}
        <form method="POST" action="{{ route('admin.absensi.koreksi.update', $attendance) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="hadir" {{ old('status', $attendance->status) === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="telat" {{ old('status', $attendance->status) === 'telat' ? 'selected' : '' }}>Telat</option>
                    <option value="alpha" {{ old('status', $attendance->status) === 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Check-in</label>
                    {{-- format('H:i'): ambil jam:menit saja dari datetime --}}
                    <input type="time" name="waktu_checkin"
                        value="{{ old('waktu_checkin', $attendance->waktu_checkin?->format('H:i')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika alpha</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Check-out</label>
                    <input type="time" name="waktu_checkout"
                        value="{{ old('waktu_checkout', $attendance->waktu_checkout?->format('H:i')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Keterangan / Alasan Koreksi <span class="text-red-500">*</span>
                </label>
                {{-- Keterangan wajib diisi supaya ada audit trail kenapa data dikoreksi --}}
                <textarea name="keterangan" rows="3" required
                    placeholder="cth: Mesin absen rusak, data dikoreksi sesuai laporan SPV"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('keterangan') border-red-400 @enderror">{{ old('keterangan', $attendance->keterangan) }}</textarea>
                @error('keterangan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">
                    Keterangan ini dicatat sebagai bukti koreksi (audit trail).
                </p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                    Simpan Koreksi
                </button>
                <a href="{{ route('admin.absensi.index') }}"
                    class="text-gray-600 hover:text-gray-800 px-6 py-2.5 rounded-lg text-sm border border-gray-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
