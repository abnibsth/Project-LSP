@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Rekap Absensi</h1>
        <p class="page-subtitle">
            Data kehadiran
            <span class="font-semibold text-ink">{{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}</span>
            s/d
            <span class="font-semibold text-ink">{{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d F Y') }}</span>
        </p>
    </div>

    <form method="GET" class="ui-card p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Karyawan</label>
            <select name="employee_id" class="form-select w-48">
                <option value="">Semua Karyawan</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="telat" {{ request('status') === 'telat' ? 'selected' : '' }}>Telat</option>
                <option value="alpha" {{ request('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
            </select>
        </div>
        <button type="submit" class="btn btn-ghost">Filter</button>
        <a href="{{ route('admin.absensi.index') }}" class="text-sm text-ink-muted-48 hover:text-ink px-2 py-2">Reset</a>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Status</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th class="!text-right">Menit Telat</th>
                    <th class="!text-center">Koreksi?</th>
                    <th class="!text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td class="text-ink-muted-80">
                            {{ $attendance->tanggal->translatedFormat('D, d M Y') }}
                        </td>
                        <td>
                            <p class="font-medium text-ink">{{ $attendance->employee->nama }}</p>
                            <p class="text-xs text-ink-muted-48">{{ $attendance->employee->departemen }}</p>
                        </td>
                        <td>
                            <span class="badge
                                @if($attendance->status === 'hadir') badge-success
                                @elseif($attendance->status === 'telat') badge-warning
                                @else badge-danger
                                @endif">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td class="font-mono text-xs text-ink-muted-80">
                            {{ $attendance->waktu_checkin?->format('H:i') ?? '-' }}
                        </td>
                        <td class="font-mono text-xs text-ink-muted-80">
                            {{ $attendance->waktu_checkout?->format('H:i') ?? '-' }}
                        </td>
                        <td class="text-right text-xs">
                            @if($attendance->menit_terlambat > 0)
                                <span class="text-amber-600 font-medium">{{ $attendance->menit_terlambat }} mnt</span>
                            @else
                                <span class="text-ink-muted-48">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($attendance->is_koreksi)
                                <span class="text-xs text-primary font-medium">Sudah dikoreksi</span>
                            @else
                                <span class="text-ink-muted-48 text-xs">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.absensi.koreksi', $attendance) }}" class="btn btn-action-soft">Koreksi</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="!text-center !py-12 text-ink-muted-48">
                            Tidak ada data absensi untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-divider-soft">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
@endsection
