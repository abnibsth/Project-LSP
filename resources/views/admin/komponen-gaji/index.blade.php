@extends('layouts.app')

@section('title', 'Komponen Gaji')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Menampilkan daftar semua komponen gaji yang berlaku di perusahaan.
        Dibagi 2 tabel: Tunjangan (menambah gaji) dan Potongan (mengurangi gaji).
        Admin bisa tambah, edit, atau nonaktifkan komponen.
    --}}

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Komponen Gaji</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola tunjangan dan potongan yang berlaku untuk semua karyawan.</p>
        </div>
        {{-- Tombol ini mengarahkan ke form tambah komponen baru --}}
        <a href="{{ route('admin.komponen-gaji.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
            + Tambah Komponen
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- =====================================================
             TABEL TUNJANGAN
             Tunjangan = komponen yang MENAMBAH gaji karyawan.
             Contoh: Tunjangan Makan, Tunjangan Transport
             ===================================================== --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <span class="text-green-600 text-lg">💰</span>
                <h2 class="font-semibold text-gray-800">Tunjangan</h2>
                {{-- Badge menampilkan jumlah tunjangan yang aktif --}}
                <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                    {{ $tunjangan->where('is_aktif', true)->count() }} aktif
                </span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama Komponen</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-right">Nilai</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tunjangan as $komponen)
                        <tr class="hover:bg-gray-50 {{ !$komponen->is_aktif ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $komponen->nama_komponen }}</p>
                                @if($komponen->keterangan)
                                    <p class="text-xs text-gray-400">{{ $komponen->keterangan }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{-- Jenis nilai: nominal (angka tetap) atau persentase (% dari gaji pokok) --}}
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $komponen->jenis_nilai === 'persentase' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($komponen->jenis_nilai) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                {{-- Tampilkan nilai sesuai jenisnya --}}
                                @if($komponen->jenis_nilai === 'persentase')
                                    {{ number_format($komponen->nilai, 0) }}%
                                @else
                                    Rp {{ number_format($komponen->nilai, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $komponen->is_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                    {{ $komponen->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex gap-1.5 justify-center items-center">
                                    <a href="{{ route('admin.komponen-gaji.edit', $komponen) }}"
                                        class="px-2.5 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all rounded-sm text-[10px] font-bold active:scale-95">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.komponen-gaji.destroy', $komponen) }}"
                                        onsubmit="return confirm('Hapus komponen &quot;{{ $komponen->nama_komponen }}&quot; secara permanen?')" class="m-0 p-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 transition-all rounded-sm text-[10px] font-bold active:scale-95 cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Belum ada data tunjangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- =====================================================
             TABEL POTONGAN
             Potongan = komponen yang MENGURANGI gaji karyawan.
             Contoh: Kasbon, Pinjaman
             ===================================================== --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <span class="text-red-500 text-lg">✂️</span>
                <h2 class="font-semibold text-gray-800">Potongan</h2>
                <span class="ml-auto text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">
                    {{ $potongan->where('is_aktif', true)->count() }} aktif
                </span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama Komponen</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-right">Nilai</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($potongan as $komponen)
                        <tr class="hover:bg-gray-50 {{ !$komponen->is_aktif ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $komponen->nama_komponen }}</p>
                                @if($komponen->keterangan)
                                    <p class="text-xs text-gray-400">{{ $komponen->keterangan }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $komponen->jenis_nilai === 'persentase' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ ucfirst($komponen->jenis_nilai) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                @if($komponen->jenis_nilai === 'persentase')
                                    {{ number_format($komponen->nilai, 0) }}%
                                @else
                                    Rp {{ number_format($komponen->nilai, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $komponen->is_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                    {{ $komponen->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex gap-1.5 justify-center items-center">
                                    <a href="{{ route('admin.komponen-gaji.edit', $komponen) }}"
                                        class="px-2.5 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all rounded-sm text-[10px] font-bold active:scale-95">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.komponen-gaji.destroy', $komponen) }}"
                                        onsubmit="return confirm('Hapus komponen &quot;{{ $komponen->nama_komponen }}&quot; secara permanen?')" class="m-0 p-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 transition-all rounded-sm text-[10px] font-bold active:scale-95 cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Belum ada data potongan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info box menjelaskan perbedaan nominal vs persentase --}}
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
        <strong>ℹ️ Keterangan:</strong>
        Jenis <strong>Nominal</strong> = nilai tetap dalam rupiah (misal: Rp 500.000).
        Jenis <strong>Persentase</strong> = dihitung dari gaji pokok karyawan (misal: 10% dari gaji pokok).
    </div>
@endsection
