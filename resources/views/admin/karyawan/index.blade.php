@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Karyawan</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola data seluruh karyawan perusahaan.</p>
        </div>
        <a href="{{ route('admin.karyawan.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
            + Tambah Karyawan
        </a>
    </div>

    {{-- Filter & Pencarian --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm mb-4 flex flex-wrap gap-3">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau NIK..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 w-56">
        <select name="departemen" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Departemen</option>
            @foreach($departemen as $dep)
                <option value="{{ $dep }}" {{ request('departemen') === $dep ? 'selected' : '' }}>{{ $dep }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Filter</button>
        <a href="{{ route('admin.karyawan.index') }}" class="text-gray-400 hover:text-gray-600 px-2 py-2 text-sm">Reset</a>
    </form>

    {{-- Tabel Daftar Karyawan --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">NIK</th>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Jabatan / Departemen</th>
                    <th class="px-6 py-3 text-left">Status Kerja</th>
                    <th class="px-6 py-3 text-right">Gaji Pokok</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50 {{ !$employee->is_aktif ? 'opacity-60' : '' }}">
                        <td class="px-6 py-3 font-mono text-gray-500">{{ $employee->nik }}</td>
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-900">{{ $employee->nama }}</p>
                            <p class="text-xs text-gray-400">{{ $employee->user?->email }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <p>{{ $employee->jabatan }}</p>
                            <p class="text-xs text-gray-400">{{ $employee->departemen }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($employee->status_kerja === 'tetap') bg-blue-100 text-blue-700
                                @elseif($employee->status_kerja === 'kontrak') bg-purple-100 text-purple-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ ucfirst($employee->status_kerja) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right font-medium">{{ $employee->gaji_pokok_format }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $employee->is_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $employee->is_aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('admin.karyawan.show', $employee) }}"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">Detail</a>
                                <a href="{{ route('admin.karyawan.edit', $employee) }}"
                                    class="text-gray-600 hover:text-gray-800 text-xs font-medium">Edit</a>
                                @if($employee->is_aktif)
                                    <form method="POST" action="{{ route('admin.karyawan.nonaktifkan', $employee) }}"
                                        onsubmit="return confirm('Nonaktifkan {{ $employee->nama }}?')">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Nonaktifkan</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.karyawan.destroy', $employee) }}"
                                        onsubmit="return confirm('Aktifkan kembali {{ $employee->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Aktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            Tidak ada data karyawan.
                            <a href="{{ route('admin.karyawan.create') }}" class="text-blue-600 ml-1">Tambah karyawan?</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
@endsection
