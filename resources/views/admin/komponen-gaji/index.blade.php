@extends('layouts.app')

@section('title', 'Komponen Gaji')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 page-header">
        <div>
            <h1 class="page-title">Komponen Gaji</h1>
            <p class="page-subtitle">Kelola tunjangan dan potongan yang berlaku untuk semua karyawan.</p>
        </div>
        <a href="{{ route('admin.komponen-gaji.create') }}" class="btn btn-primary self-start">+ Tambah Komponen</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        <div class="ui-table-wrap">
            <div class="px-5 py-4 border-b border-divider-soft flex items-center gap-2">
                <h2 class="font-semibold text-ink">Tunjangan</h2>
                <span class="ml-auto badge badge-success">
                    {{ $tunjangan->where('is_aktif', true)->count() }} aktif
                </span>
            </div>
            <table class="ui-table !min-w-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th class="!text-right">Nilai</th>
                        <th class="!text-center">Status</th>
                        <th class="!text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tunjangan as $komponen)
                        <tr class="{{ !$komponen->is_aktif ? 'opacity-50' : '' }}">
                            <td>
                                <p class="font-medium text-ink">{{ $komponen->nama_komponen }}</p>
                                @if($komponen->keterangan)
                                    <p class="text-xs text-ink-muted-48">{{ $komponen->keterangan }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $komponen->jenis_nilai === 'persentase' ? 'badge-purple' : 'badge-primary' }}">
                                    {{ ucfirst($komponen->jenis_nilai) }}
                                </span>
                            </td>
                            <td class="text-right font-medium">
                                @if($komponen->jenis_nilai === 'persentase')
                                    {{ number_format($komponen->nilai, 0) }}%
                                @else
                                    Rp {{ number_format($komponen->nilai, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $komponen->is_aktif ? 'badge-success' : 'badge-danger' }}">
                                    {{ $komponen->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-1.5 justify-center items-center">
                                    <a href="{{ route('admin.komponen-gaji.edit', $komponen) }}" class="btn btn-muted-soft">Edit</a>
                                    <form method="POST" action="{{ route('admin.komponen-gaji.destroy', $komponen) }}"
                                        onsubmit="return confirm('Hapus komponen &quot;{{ $komponen->nama_komponen }}&quot; secara permanen?')" class="m-0 p-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger-soft">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="!text-center !py-8 text-ink-muted-48 text-sm">Belum ada data tunjangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ui-table-wrap">
            <div class="px-5 py-4 border-b border-divider-soft flex items-center gap-2">
                <h2 class="font-semibold text-ink">Potongan</h2>
                <span class="ml-auto badge badge-danger">
                    {{ $potongan->where('is_aktif', true)->count() }} aktif
                </span>
            </div>
            <table class="ui-table !min-w-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th class="!text-right">Nilai</th>
                        <th class="!text-center">Status</th>
                        <th class="!text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($potongan as $komponen)
                        <tr class="{{ !$komponen->is_aktif ? 'opacity-50' : '' }}">
                            <td>
                                <p class="font-medium text-ink">{{ $komponen->nama_komponen }}</p>
                                @if($komponen->keterangan)
                                    <p class="text-xs text-ink-muted-48">{{ $komponen->keterangan }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $komponen->jenis_nilai === 'persentase' ? 'badge-purple' : 'badge-warning' }}">
                                    {{ ucfirst($komponen->jenis_nilai) }}
                                </span>
                            </td>
                            <td class="text-right font-medium">
                                @if($komponen->jenis_nilai === 'persentase')
                                    {{ number_format($komponen->nilai, 0) }}%
                                @else
                                    Rp {{ number_format($komponen->nilai, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $komponen->is_aktif ? 'badge-success' : 'badge-danger' }}">
                                    {{ $komponen->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-1.5 justify-center items-center">
                                    <a href="{{ route('admin.komponen-gaji.edit', $komponen) }}" class="btn btn-muted-soft">Edit</a>
                                    <form method="POST" action="{{ route('admin.komponen-gaji.destroy', $komponen) }}"
                                        onsubmit="return confirm('Hapus komponen &quot;{{ $komponen->nama_komponen }}&quot; secara permanen?')" class="m-0 p-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger-soft">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="!text-center !py-8 text-ink-muted-48 text-sm">Belum ada data potongan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        <span>
            <strong>Keterangan:</strong>
            <strong>Nominal</strong> = nilai tetap dalam rupiah.
            <strong>Persentase</strong> = dihitung dari gaji pokok karyawan.
        </span>
    </div>
@endsection
