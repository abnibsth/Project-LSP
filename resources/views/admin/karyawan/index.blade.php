@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 page-header">
        <div>
            <h1 class="page-title">Data Karyawan</h1>
            <p class="page-subtitle">Kelola data seluruh karyawan perusahaan.</p>
        </div>
        <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary self-start">+ Tambah Karyawan</a>
    </div>

    <form method="GET" class="filter-bar">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau NIK..."
            class="form-input w-full sm:w-56">
        <select name="departemen" class="form-select w-full sm:w-auto">
            <option value="">Semua Departemen</option>
            @foreach($departemen as $dep)
                <option value="{{ $dep }}" {{ request('departemen') === $dep ? 'selected' : '' }}>{{ $dep }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select w-full sm:w-auto">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="btn btn-ghost">Filter</button>
        <a href="{{ route('admin.karyawan.index') }}" class="text-sm text-ink-muted-48 hover:text-ink px-2">Reset</a>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jabatan / Departemen</th>
                    <th>Status Kerja</th>
                    <th class="!text-right">Gaji Pokok</th>
                    <th>Status</th>
                    <th class="!text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr class="{{ !$employee->is_aktif ? 'opacity-60' : '' }}">
                        <td class="font-mono text-ink-muted-48">{{ $employee->nik }}</td>
                        <td>
                            <p class="font-medium text-ink">{{ $employee->nama }}</p>
                            <p class="text-xs text-ink-muted-48">{{ $employee->user?->email }}</p>
                        </td>
                        <td>
                            <p>{{ $employee->jabatan }}</p>
                            <p class="text-xs text-ink-muted-48">{{ $employee->departemen }}</p>
                        </td>
                        <td>
                            <span class="badge
                                @if($employee->status_kerja === 'tetap') badge-primary
                                @elseif($employee->status_kerja === 'kontrak') badge-purple
                                @else badge-muted
                                @endif">
                                {{ ucfirst($employee->status_kerja) }}
                            </span>
                        </td>
                        <td class="text-right font-medium">{{ $employee->gaji_pokok_format }}</td>
                        <td>
                            <span class="badge {{ $employee->is_aktif ? 'badge-success' : 'badge-danger' }}">
                                {{ $employee->is_aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.karyawan.show', $employee) }}" class="btn btn-action-soft">Detail</a>
                                <a href="{{ route('admin.karyawan.edit', $employee) }}" class="btn btn-muted-soft">Edit</a>
                                @if($employee->is_aktif)
                                    <form method="POST" action="{{ route('admin.karyawan.nonaktifkan', $employee) }}"
                                        onsubmit="return confirm('Nonaktifkan {{ $employee->nama }}?')" class="m-0 p-0 flex">
                                        @csrf
                                        <button type="submit" class="btn btn-danger-soft">Nonaktifkan</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.karyawan.destroy', $employee) }}"
                                        onsubmit="return confirm('Aktifkan kembali {{ $employee->nama }}?')" class="m-0 p-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-success-soft">Aktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="!text-center !py-10 text-ink-muted-48">
                            Tidak ada data karyawan.
                            <a href="{{ route('admin.karyawan.create') }}" class="text-primary ml-1">Tambah karyawan?</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-divider-soft">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
@endsection
