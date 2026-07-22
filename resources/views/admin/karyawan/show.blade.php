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
            <a href="{{ route('admin.karyawan.index') }}" class="back-link">← Kembali ke Daftar Karyawan</a>
            <h1 class="page-title">{{ $employee->nama }}</h1>
            <p class="page-subtitle font-mono">NIK: {{ $employee->nik }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.karyawan.edit', $employee) }}" class="btn btn-ghost">
                Edit Data
            </a>
            @if($employee->is_aktif)
                <form method="POST" action="{{ route('admin.karyawan.nonaktifkan', $employee) }}"
                    onsubmit="return confirm('Nonaktifkan {{ $employee->nama }}?')">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft !px-3 !py-2 !text-xs">
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
            <div class="ui-card ui-card-pad">
                <h2 class="section-title mb-4">Informasi Pekerjaan</h2>
                <dl class="info-list">
                    <div class="info-row">
                        <dt>Jabatan</dt>
                        <dd>{{ $employee->jabatan }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Departemen</dt>
                        <dd>{{ $employee->departemen }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Status Kerja</dt>
                        <dd>
                            <span class="badge
                                @if($employee->status_kerja === 'tetap') badge-primary
                                @elseif($employee->status_kerja === 'kontrak') badge-purple
                                @else badge-muted
                                @endif">
                                {{ ucfirst($employee->status_kerja) }}
                            </span>
                        </dd>
                    </div>
                    <div class="info-row">
                        <dt>Gaji Pokok</dt>
                        <dd class="font-semibold">{{ $employee->gaji_pokok_format }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Tgl Masuk</dt>
                        {{-- translatedFormat: format tanggal dalam bahasa lokal --}}
                        <dd class="!font-normal text-ink-muted-80">{{ $employee->tanggal_masuk->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Status</dt>
                        <dd>
                            <span class="badge {{ $employee->is_aktif ? 'badge-success' : 'badge-danger' }}">
                                {{ $employee->is_aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Card: Info Kontak --}}
            <div class="ui-card ui-card-pad">
                <h2 class="section-title mb-4">Kontak & Rekening</h2>
                <dl class="info-stack">
                    <div class="info-stack-item">
                        <dt>Email Login</dt>
                        <dd>{{ $employee->user?->email ?? '-' }}</dd>
                    </div>
                    <div class="info-stack-item">
                        <dt>No. Telepon</dt>
                        <dd>{{ $employee->no_telepon ?? '-' }}</dd>
                    </div>
                    <div class="info-stack-item">
                        <dt>Rekening</dt>
                        <dd>
                            @if($employee->no_rekening)
                                {{ $employee->nama_bank }} — {{ $employee->no_rekening }}
                            @else
                                <span class="text-ink-muted-48 font-normal">Belum diisi</span>
                            @endif
                        </dd>
                    </div>
                    <div class="info-stack-item">
                        <dt>Alamat</dt>
                        <dd class="!font-normal text-ink-muted-80 text-xs leading-relaxed">{{ $employee->alamat ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- =====================================================
             KOLOM KANAN: Riwayat Absensi & Slip Gaji
             ===================================================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tabel: 10 Absensi Terakhir --}}
            <div class="ui-table-wrap">
                <div class="ui-card-header">
                    <h2 class="section-title">10 Absensi Terakhir</h2>
                </div>
                {{-- min-w: agar kolom tidak bertumpuk di layar kecil; wrap table bisa di-scroll horizontal --}}
                <table class="ui-table !min-w-[520px]">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th class="!text-right">Menit Telat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->attendances as $att)
                            <tr>
                                <td class="text-ink-muted-80 text-xs">
                                    {{ $att->tanggal->translatedFormat('D, d M Y') }}
                                </td>
                                <td>
                                    <span class="badge
                                        @if($att->status === 'hadir') badge-success
                                        @elseif($att->status === 'telat') badge-warning
                                        @else badge-danger
                                        @endif">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                </td>
                                <td class="font-mono text-xs text-ink-muted-80">
                                    {{ $att->waktu_checkin?->format('H:i') ?? '-' }}
                                </td>
                                <td class="font-mono text-xs text-ink-muted-80">
                                    {{ $att->waktu_checkout?->format('H:i') ?? '-' }}
                                </td>
                                <td class="text-right text-xs">
                                    {{ $att->menit_terlambat > 0 ? $att->menit_terlambat.' mnt' : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-cell">Belum ada data absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tabel: Riwayat Slip Gaji --}}
            <div class="ui-table-wrap">
                <div class="ui-card-header">
                    <h2 class="section-title">Riwayat Slip Gaji</h2>
                </div>
                {{-- min-w: jaga kolom Aksi di kanan tidak tertekan di mobile --}}
                <table class="ui-table !min-w-[600px]">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th class="!text-right">Gaji Bruto</th>
                            <th class="!text-right">Potongan</th>
                            <th class="!text-right">Gaji Bersih</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->payslips as $slip)
                            <tr>
                                <td class="text-ink-muted-80">
                                    {{ $slip->payrollPeriod->label }}
                                </td>
                                <td class="text-right text-ink-muted-80 text-xs tabular-nums">
                                    Rp {{ number_format($slip->gaji_bruto, 0, ',', '.') }}
                                </td>
                                <td class="text-right text-red-600 text-xs tabular-nums">
                                    − Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}
                                </td>
                                <td class="text-right font-semibold text-ink tabular-nums">
                                    Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.slip-gaji.show', $slip) }}" class="btn btn-action-soft">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-cell">Belum ada data slip gaji.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
