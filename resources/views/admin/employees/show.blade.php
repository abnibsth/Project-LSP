@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Menampilkan detail lengkap satu karyawan:
        - Informasi pribadi & pekerjaan
        - 10 data absensi terakhir
        - Daftar slip gaji
    --}}

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.karyawan.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Kembali ke Daftar Karyawan
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $employee->nama }}</h1>
            <p class="text-gray-500 text-sm font-mono">NIK: {{ $employee->nik }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.karyawan.edit', $employee) }}"
                class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition-colors">
                ✎ Edit Data
            </a>
            @if($employee->is_aktif)
                <form method="POST" action="{{ route('admin.karyawan.nonaktifkan', $employee) }}"
                    onsubmit="return confirm('Nonaktifkan {{ $employee->nama }}?')">
                    @csrf
                    <button type="submit"
                        class="bg-red-50 border border-red-200 hover:bg-red-100 text-red-600 font-medium px-4 py-2 rounded-lg text-sm">
                        Nonaktifkan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- =====================================================
             KOLOM KIRI: Informasi Karyawan
             ===================================================== --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Card: Info Pekerjaan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4">Informasi Pekerjaan</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Jabatan</dt>
                        <dd class="font-medium text-gray-900">{{ $employee->jabatan }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Departemen</dt>
                        <dd class="font-medium text-gray-900">{{ $employee->departemen }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status Kerja</dt>
                        <dd>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($employee->status_kerja === 'tetap') bg-blue-100 text-blue-700
                                @elseif($employee->status_kerja === 'kontrak') bg-purple-100 text-purple-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ ucfirst($employee->status_kerja) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Gaji Pokok</dt>
                        <dd class="font-bold text-gray-900">{{ $employee->gaji_pokok_format }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tgl Masuk</dt>
                        {{-- translatedFormat: format tanggal dalam bahasa lokal --}}
                        <dd class="text-gray-700">{{ $employee->tanggal_masuk->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $employee->is_aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $employee->is_aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Card: Info Kontak --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4">Kontak & Rekening</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500 mb-0.5">Email Login</dt>
                        <dd class="font-medium text-gray-900">{{ $employee->user?->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-0.5">No. Telepon</dt>
                        <dd class="font-medium text-gray-900">{{ $employee->no_telepon ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-0.5">Rekening</dt>
                        <dd class="font-medium text-gray-900">
                            @if($employee->no_rekening)
                                {{ $employee->nama_bank }} — {{ $employee->no_rekening }}
                            @else
                                <span class="text-gray-400">Belum diisi</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-0.5">Alamat</dt>
                        <dd class="text-gray-700 text-xs">{{ $employee->alamat ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- =====================================================
             KOLOM KANAN: Riwayat Absensi & Slip Gaji
             ===================================================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tabel: 10 Absensi Terakhir --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">10 Absensi Terakhir</h2>
                </div>
                {{-- overflow-x-auto: Agar tabel bisa di-scroll secara horizontal jika ukuran layar lebih kecil dari lebar tabel, sehingga tidak terpotong --}}
                <div class="overflow-x-auto">
                    {{-- min-w-[500px]: Menentukan lebar minimal tabel absensi agar kolom-kolomnya tetap proporsional dan teksnya tidak bertumpuk --}}
                    <table class="w-full text-sm min-w-[500px]">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left">Tanggal</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Check-in</th>
                                <th class="px-6 py-3 text-left">Check-out</th>
                                <th class="px-6 py-3 text-right">Menit Telat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employee->attendances as $att)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-2.5 text-gray-600 text-xs">
                                        {{ $att->tanggal->translatedFormat('D, d M Y') }}
                                    </td>
                                    <td class="px-6 py-2.5">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($att->status === 'hadir') bg-green-100 text-green-700
                                            @elseif($att->status === 'telat') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-600
                                            @endif">
                                            {{ ucfirst($att->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-2.5 font-mono text-xs text-gray-600">
                                        {{ $att->waktu_checkin?->format('H:i') ?? '-' }}
                                        @if($att->latitude_checkin && $att->longitude_checkin)
                                            <a href="https://www.google.com/maps?q={{ $att->latitude_checkin }},{{ $att->longitude_checkin }}"
                                               target="_blank"
                                               title="Lihat lokasi check-in"
                                               class="ml-1 text-blue-500 hover:text-blue-700">📍 Map</a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-2.5 font-mono text-xs text-gray-600">
                                        {{ $att->waktu_checkout?->format('H:i') ?? '-' }}
                                        @if($att->latitude_checkout && $att->longitude_checkout)
                                            <a href="https://www.google.com/maps?q={{ $att->latitude_checkout }},{{ $att->longitude_checkout }}"
                                               target="_blank"
                                               title="Lihat lokasi check-out"
                                               class="ml-1 text-blue-500 hover:text-blue-700">📍 Map</a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-2.5 text-right text-xs">
                                        {{ $att->menit_terlambat > 0 ? $att->menit_terlambat.' mnt' : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                                        Belum ada data absensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel: Riwayat Slip Gaji --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Riwayat Slip Gaji</h2>
                </div>
                {{-- overflow-x-auto: Agar tabel slip gaji dapat di-scroll horizontal dan tidak terpotong di layar kecil --}}
                <div class="overflow-x-auto">
                    {{-- min-w-[600px]: Menentukan lebar minimal tabel riwayat slip gaji agar kolom 'Aksi' (Detail) di sebelah kanan tidak tertekan/terpotong --}}
                    <table class="w-full text-sm min-w-[600px]">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left">Periode</th>
                                <th class="px-6 py-3 text-right">Gaji Bruto</th>
                                <th class="px-6 py-3 text-right">Potongan</th>
                                <th class="px-6 py-3 text-right">Gaji Bersih</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employee->payslips as $slip)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-2.5 text-gray-700">
                                        {{ $slip->payrollPeriod->label }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right text-gray-600 text-xs">
                                        Rp {{ number_format($slip->gaji_bruto, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right text-red-500 text-xs">
                                        - Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-2.5 text-right font-bold text-gray-900">
                                        Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-2.5 text-center">
                                        <a href="{{ route('admin.slip-gaji.show', $slip) }}"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                                        Belum ada data slip gaji.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
