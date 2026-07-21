@extends('layouts.app')

@section('title', 'Detail Slip Gaji')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Menampilkan rincian lengkap satu slip gaji karyawan:
        - Ringkasan gaji (bruto, potongan, bersih)
        - Rincian komponen per baris (tunjangan dan potongan)
        - Info kehadiran bulan ini
        Admin juga bisa download PDF dari sini.
    --}}

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.slip-gaji.index') }}" class="text-sm text-ink-muted-48 hover:text-ink-muted-80">
                ← Kembali ke Daftar Slip Gaji
            </a>
            <h1 class="page-title mt-2">
                Slip Gaji — {{ $payslip->employee->nama }}
            </h1>
            <p class="text-ink-muted-48 text-sm">
                Periode: {{ $payslip->payrollPeriod->label }} ·
                NIK: <span class="font-mono">{{ $payslip->employee->nik }}</span>
            </p>
        </div>
        {{-- Tombol download PDF --}}
        <a href="{{ route('admin.slip-gaji.download', $payslip) }}"
            class="bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors flex items-center gap-2">
            ⬇️ Download PDF
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: Ringkasan Gaji --}}
        <div class="space-y-4">
            {{-- Card Ringkasan --}}
            <div class="bg-white rounded-xl border border-hairline shadow-sm p-5">
                <h2 class="font-semibold text-ink mb-4">Ringkasan Gaji</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-ink-muted-48">Gaji Pokok</dt>
                        <dd class="font-medium">Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between text-green-600">
                        <dt>+ Total Tunjangan</dt>
                        <dd class="font-medium">Rp {{ number_format($payslip->total_tunjangan, 0, ',', '.') }}</dd>
                    </div>
                    <div class="border-t border-divider-soft pt-3 flex justify-between font-semibold">
                        <dt class="text-ink-muted-80">Gaji bruto </dt>
                        <dd>Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}</dd>
                    </div>

                    <div class="flex justify-between text-red-500">
                        <dt>- Potongan Absensi</dt>
                        <dd>Rp {{ number_format($payslip->potongan_absensi, 0, ',', '.') }}</dd>
                    </div>
                    <!-- ini adalah perluangan - -->
                    @forelse($payslip->components->where('tipe', 'potongan') as $comp)
                        @if(!str_contains(strtolower($comp->nama_komponen), 'kehadiran') && !str_contains(strtolower($comp->nama_komponen), 'pajak'))
                            <div class="flex justify-between text-red-500">
                                <dt>- {{ $comp->keterangan ?: $comp->nama_komponen }}</dt>
                                <dd>Rp {{ number_format($comp->nilai, 0, ',', '.') }}</dd>
                            </div>
                        @endif
                    @empty
                        {{-- Tidak ada potongan lain --}}
                    @endforelse
                    <div class="border-t border-divider-soft pt-3 flex justify-between font-bold text-lg">
                        <dt class="text-ink">Gaji Bersih</dt>
                        <dd class="text-green-600">Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Card Info Kehadiran (dari detail_json) --}}
            @if($payslip->detail_json)
                <div class="bg-white rounded-xl border border-hairline shadow-sm p-5">
                    <h2 class="font-semibold text-ink mb-4">Rekap Kehadiran</h2>
                    {{-- detail_json berisi array data kehadiran yang disimpan saat payroll diproses --}}
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-ink-muted-48">Total Hari Hadir</dt>
                            <dd class="font-medium text-green-600">{{ $payslip->detail_json['total_hadir'] }} hari</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-muted-48">Telat</dt>
                            <dd class="font-medium text-yellow-600">{{ $payslip->detail_json['total_telat'] }} kali</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-muted-48">Alpha</dt>
                            <dd class="font-medium text-red-600">{{ $payslip->detail_json['total_alpha'] }} hari</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-muted-48">Total Menit Telat</dt>
                            <dd class="font-medium">{{ $payslip->detail_json['total_menit_telat'] }} menit</dd>
                        </div>
                    </dl>
                </div>
            @endif
        </div>

        {{-- =====================================================
             KOLOM KANAN: Rincian Komponen
             ===================================================== --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-hairline shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-divider-soft">
                    <h2 class="font-semibold text-ink">Rincian Komponen Gaji</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-canvas-parchment text-ink-muted-48 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Komponen</th>
                            <th class="px-6 py-3 text-left">Tipe</th>
                            <th class="px-6 py-3 text-right">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider-soft">
                        {{-- Tampilkan tunjangan dulu --}}
                        @foreach($payslip->components->where('tipe', 'tunjangan') as $comp)
                            <tr class="hover:bg-canvas-parchment">
                                <td class="px-6 py-3 text-ink-muted-80">{{ $comp->nama_komponen }}</td>
                                <td class="px-6 py-3">
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Tunjangan</span>
                                </td>
                                <td class="px-6 py-3 text-right text-green-600 font-medium">
                                    + Rp {{ number_format($comp->nilai, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        {{-- Lalu tampilkan potongan --}}
                        @foreach($payslip->components->where('tipe', 'potongan') as $comp)
                            <tr class="hover:bg-canvas-parchment">
                                <td class="px-6 py-3 text-ink-muted-80">{{ $comp->nama_komponen }}</td>
                                <td class="px-6 py-3">
                                    <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Potongan</span>
                                </td>
                                <td class="px-6 py-3 text-right text-red-500 font-medium">
                                    - Rp {{ number_format($comp->nilai, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        @if($payslip->components->isEmpty())
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-ink-muted-48 text-sm">
                                    Tidak ada rincian komponen.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
