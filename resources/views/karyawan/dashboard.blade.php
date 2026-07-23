@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    {{--
        FUNGSI HALAMAN INI:
        Home harian karyawan.
        Struktur (atas → bawah):
        1. Banner welcome + identitas
        2. 3 kartu ringkas gaji
        3. Status absensi hari ini
        4. Breakdown slip terakhir (penghasilan | potongan)
        5. Kehadiran bulan ini + aksi cepat
        6. Absensi terbaru + riwayat gaji

        Catatan desain:
        - Pakai design system app (ui-card, badge, btn, Lucide)
        - Bukan copy 1:1 mockup biru flat
        - Data hanya dari controller (jangan query di Blade)
    --}}

    @if(!$employee)
        {{-- Kasus edge: akun login ada, tapi belum di-link ke tabel karyawan --}}
        <div class="alert alert-warning">
            <span class="alert-icon" aria-hidden="true">!</span>
            <span>Data karyawan belum terhubung ke akun ini. Hubungi Admin/HRD.</span>
        </div>
    @else
        {{-- =====================================================
             BAGIAN 1: Banner welcome
             Memberi konteks "siapa saya" di sistem
             ===================================================== --}}
        <div class="dash-welcome mb-5">
            <div class="min-w-0">
                <p class="dash-welcome-kicker">Selamat datang,</p>
                <h1 class="dash-welcome-name">{{ $employee->nama }}</h1>
                <p class="dash-welcome-role">
                    {{ $employee->jabatan }}
                    <span class="dash-welcome-dot" aria-hidden="true">·</span>
                    {{ $employee->departemen }}
                </p>
            </div>

            {{-- Meta singkat: status kerja, NIK, tanggal masuk --}}
            <div class="dash-welcome-meta">
                <div>
                    <p class="dash-welcome-meta-label">Status Kerja</p>
                    <p class="dash-welcome-meta-value">{{ ucfirst($employee->status_kerja) }}</p>
                </div>
                <div>
                    <p class="dash-welcome-meta-label">NIK</p>
                    <p class="dash-welcome-meta-value font-mono">{{ $employee->nik }}</p>
                </div>
                <div>
                    <p class="dash-welcome-meta-label">Bergabung</p>
                    <p class="dash-welcome-meta-value">
                        {{ $employee->tanggal_masuk?->translatedFormat('d F Y') ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- =====================================================
             BAGIAN 2: 3 kartu ringkas gaji
             - Gaji bersih terakhir
             - Rata-rata gaji bersih (semua slip final)
             - Total diterima tahun ini (YTD)
             ===================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-5">
            <div class="stat-card">
                <div class="flex items-start justify-between gap-3">
                    <div class="stat-icon">
                        <i data-lucide="wallet" class="ui-icon" aria-hidden="true"></i>
                    </div>
                    @if($slipTerakhir)
                        <span class="badge badge-muted">{{ $slipTerakhir->payrollPeriod->label }}</span>
                    @endif
                </div>
                <p class="text-[1.35rem] font-semibold text-ink mt-3 tracking-tight tabular-nums">
                    @if($slipTerakhir)
                        {{ $slipTerakhir->gaji_bersih_format }}
                    @else
                        —
                    @endif
                </p>
                <p class="text-xs text-ink-muted-48 mt-1">Gaji Bersih Terakhir</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i data-lucide="trending-up" class="ui-icon" aria-hidden="true"></i>
                </div>
                <p class="text-[1.35rem] font-semibold text-ink mt-3 tracking-tight tabular-nums">
                    @if($rataRataGajiBersih > 0)
                        Rp {{ number_format($rataRataGajiBersih, 0, ',', '.') }}
                    @else
                        —
                    @endif
                </p>
                <p class="text-xs text-ink-muted-48 mt-1">Rata-rata Gaji Bersih</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i data-lucide="calendar-range" class="ui-icon" aria-hidden="true"></i>
                </div>
                <p class="text-[1.35rem] font-semibold text-ink mt-3 tracking-tight tabular-nums">
                    @if($totalGajiYtd > 0)
                        Rp {{ number_format($totalGajiYtd, 0, ',', '.') }}
                    @else
                        —
                    @endif
                </p>
                <p class="text-xs text-ink-muted-48 mt-1">Total Diterima ({{ now()->year }})</p>
            </div>
        </div>

        {{-- =====================================================
             BAGIAN 3: Absensi hari ini (aksi utama harian)
             3 state: belum check-in | sudah check-in | selesai
             ===================================================== --}}
        <div class="ui-card ui-card-pad mb-5">
            <p class="section-label mb-4">
                Absensi Hari Ini — {{ now()->translatedFormat('d F Y') }}
            </p>

            @if(!$absensiHariIni)
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon">
                        <i data-lucide="calendar-clock" class="ui-icon" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="section-title">Belum check-in</p>
                        <p class="text-sm text-ink-muted-48 mt-0.5">Catat jam masuk kerja untuk hari ini.</p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkin') }}" class="sm:ml-auto">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full sm:w-auto">Check-In</button>
                    </form>
                </div>
            @elseif(!$absensiHariIni->waktu_checkout)
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon is-success">
                        <i data-lucide="clipboard-check" class="ui-icon" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="section-title">Sudah check-in</p>
                        <p class="text-sm text-ink-muted-48 mt-0.5">
                            Masuk pukul {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                            —
                            <span class="font-semibold {{ $absensiHariIni->status === 'telat' ? 'text-amber-600' : 'text-primary' }}">
                                {{ ucfirst($absensiHariIni->status) }}
                                @if($absensiHariIni->status === 'telat')
                                    ({{ $absensiHariIni->menit_terlambat }} menit)
                                @endif
                            </span>
                        </p>
                    </div>
                    <form method="POST" action="{{ route('karyawan.absensi.checkout') }}" class="sm:ml-auto">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full sm:w-auto">Check-Out</button>
                    </form>
                </div>
            @else
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="stat-icon is-success">
                        <i data-lucide="circle-check" class="ui-icon" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="section-title">Kehadiran selesai</p>
                        <p class="text-sm text-ink-muted-48 mt-0.5">
                            Masuk {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                            — Pulang {{ $absensiHariIni->waktu_checkout->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- =====================================================
             BAGIAN 4: Breakdown slip gaji terakhir
             Layout 2 kolom: penghasilan (kiri) | potongan (kanan)
             Mirip referensi, tapi class design system PT Nikel
             ===================================================== --}}
        <div class="ui-card ui-card-pad mb-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-5">
                <div>
                    <h2 class="section-title">
                        Slip Gaji Terakhir
                        @if($slipTerakhir)
                            — {{ $slipTerakhir->payrollPeriod->label }}
                        @endif
                    </h2>
                    <p class="text-xs text-ink-muted-48 mt-1">Rincian penghasilan dan potongan dari periode final terakhir.</p>
                </div>
                @if($slipTerakhir)
                    <a href="{{ route('karyawan.slip-gaji.show', $slipTerakhir) }}" class="text-sm text-primary font-medium hover:underline inline-flex items-center gap-1">
                        Lihat semua
                        <span aria-hidden="true">→</span>
                    </a>
                @endif
            </div>

            @if($slipTerakhir)
                {{--
                    components di-eager-load di controller.
                    Filter koleksi di Blade (where tipe) aman karena data sudah di memory.
                --}}
                @php
                    $tunjangan = $slipTerakhir->components->where('tipe', 'tunjangan');
                    $potongan = $slipTerakhir->components->where('tipe', 'potongan');
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">
                    {{-- Kolom penghasilan --}}
                    <div>
                        <p class="section-label mb-3">Penghasilan</p>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between gap-4">
                                <span class="text-ink-muted-80">Gaji Pokok</span>
                                <span class="font-medium tabular-nums text-ink">
                                    Rp {{ number_format($slipTerakhir->gaji_pokok, 0, ',', '.') }}
                                </span>
                            </div>

                            @forelse($tunjangan as $komponen)
                                <div class="flex justify-between gap-4">
                                    <span class="text-ink-muted-80">
                                        {{ $komponen->keterangan ?: $komponen->nama_komponen }}
                                    </span>
                                    <span class="font-medium tabular-nums text-ink">
                                        Rp {{ number_format($komponen->nilai, 0, ',', '.') }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-ink-muted-48">Tidak ada tunjangan tambahan.</p>
                            @endforelse

                            <div class="flex justify-between gap-4 border-t border-divider-soft pt-2.5 font-semibold">
                                <span class="text-ink">Total Penghasilan</span>
                                <span class="tabular-nums text-ink">
                                    Rp {{ number_format($slipTerakhir->gaji_bruto, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom potongan --}}
                    <div>
                        <p class="section-label mb-3">Potongan</p>
                        <div class="space-y-2.5 text-sm">
                            @forelse($potongan as $komponen)
                                <div class="flex justify-between gap-4">
                                    <span class="text-ink-muted-80">
                                        {{ $komponen->keterangan ?: $komponen->nama_komponen }}
                                    </span>
                                    <span class="font-medium tabular-nums text-red-600">
                                        (Rp {{ number_format($komponen->nilai, 0, ',', '.') }})
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-ink-muted-48">Tidak ada potongan komponen.</p>
                            @endforelse

                            {{--
                                Potongan absensi & pajak disimpan di kolom slip,
                                bukan selalu sebagai baris komponen.
                            --}}
                            @if((float) $slipTerakhir->potongan_absensi > 0)
                                <div class="flex justify-between gap-4">
                                    <span class="text-ink-muted-80">Potongan Absensi</span>
                                    <span class="font-medium tabular-nums text-red-600">
                                        (Rp {{ number_format($slipTerakhir->potongan_absensi, 0, ',', '.') }})
                                    </span>
                                </div>
                            @endif

                            @if((float) $slipTerakhir->potongan_pajak > 0)
                                <div class="flex justify-between gap-4">
                                    <span class="text-ink-muted-80">Potongan Pajak</span>
                                    <span class="font-medium tabular-nums text-red-600">
                                        (Rp {{ number_format($slipTerakhir->potongan_pajak, 0, ',', '.') }})
                                    </span>
                                </div>
                            @endif

                            <div class="flex justify-between gap-4 border-t border-divider-soft pt-2.5 font-semibold">
                                <span class="text-ink">Total Potongan</span>
                                <span class="tabular-nums text-red-600">
                                    (Rp {{ number_format($slipTerakhir->total_potongan, 0, ',', '.') }})
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Highlight take-home pay (gaji bersih) --}}
                <div class="dash-netpay mt-5">
                    <div>
                        <p class="text-sm font-semibold text-emerald-900">Gaji Bersih (Take-Home Pay)</p>
                        <p class="text-xs text-emerald-800/70 mt-0.5">
                            Setelah semua potongan · {{ $slipTerakhir->payrollPeriod->label }}
                        </p>
                    </div>
                    <p class="text-xl sm:text-2xl font-semibold text-emerald-900 tracking-tight tabular-nums">
                        {{ $slipTerakhir->gaji_bersih_format }}
                    </p>
                </div>
            @else
                <div class="empty-state !min-h-[10rem] !py-8">
                    <div class="empty-state-icon">
                        <i data-lucide="file-text" class="ui-icon" aria-hidden="true"></i>
                    </div>
                    <h3 class="empty-state-title">Belum ada slip gaji final</h3>
                    <p class="empty-state-text">
                        Slip muncul di sini setelah admin memproses dan finalisasi payroll.
                    </p>
                </div>
            @endif
        </div>

        {{-- =====================================================
             BAGIAN 5: Kehadiran bulan ini + aksi cepat
             ===================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
            <div class="ui-card ui-card-pad lg:col-span-2">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="section-label mb-0">Kehadiran Bulan Ini</p>
                    <a href="{{ route('karyawan.absensi.index') }}" class="text-xs text-primary font-medium hover:underline">
                        Lihat rekap
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-2.5">
                    <div class="metric-tile">
                        <p class="metric-tile-value text-primary tabular-nums">{{ $rekapAbsensi['hadir'] }}</p>
                        <p class="metric-tile-label">Hadir</p>
                    </div>
                    <div class="metric-tile">
                        <p class="metric-tile-value text-amber-600 tabular-nums">{{ $rekapAbsensi['telat'] }}</p>
                        <p class="metric-tile-label">Telat</p>
                    </div>
                    <div class="metric-tile">
                        <p class="metric-tile-value text-red-600 tabular-nums">{{ $rekapAbsensi['alpha'] }}</p>
                        <p class="metric-tile-label">Alpha</p>
                    </div>
                </div>
            </div>

            {{-- Shortcut navigasi biar dashboard actionable --}}
            <div class="ui-card ui-card-pad">
                <p class="section-label mb-4">Aksi Cepat</p>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('karyawan.absensi.index') }}" class="quick-action !p-3">
                        <span class="quick-action-icon !w-9 !h-9">
                            <i data-lucide="clipboard-check" class="ui-icon" aria-hidden="true"></i>
                        </span>
                        <span class="text-[11px] font-semibold text-ink">Absensi</span>
                    </a>
                    <a href="{{ route('karyawan.slip-gaji.index') }}" class="quick-action !p-3">
                        <span class="quick-action-icon !w-9 !h-9">
                            <i data-lucide="file-text" class="ui-icon" aria-hidden="true"></i>
                        </span>
                        <span class="text-[11px] font-semibold text-ink">Slip</span>
                    </a>
                    <a href="{{ route('karyawan.profil.edit') }}" class="quick-action !p-3">
                        <span class="quick-action-icon !w-9 !h-9">
                            <i data-lucide="user-round" class="ui-icon" aria-hidden="true"></i>
                        </span>
                        <span class="text-[11px] font-semibold text-ink">Profil</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- =====================================================
             BAGIAN 6: Absensi terbaru + riwayat gaji bersih
             Mengisi whitespace bawah agar dashboard terasa utuh
             ===================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            <div class="ui-table-wrap lg:col-span-3">
                <div class="ui-card-header flex items-center justify-between gap-3">
                    <h2 class="section-title">Absensi Terbaru</h2>
                    <a href="{{ route('karyawan.absensi.index') }}" class="text-xs text-primary font-medium hover:underline">
                        Semua
                    </a>
                </div>
                <table class="ui-table ui-table-compact">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensiTerbaru as $absensi)
                            <tr>
                                <td class="text-ink-muted-80 text-xs">
                                    {{ $absensi->tanggal->translatedFormat('D, d M Y') }}
                                </td>
                                <td class="font-mono text-xs text-ink-muted-80">
                                    {{ $absensi->waktu_checkin?->format('H:i') ?? '—' }}
                                </td>
                                <td class="font-mono text-xs text-ink-muted-80">
                                    {{ $absensi->waktu_checkout?->format('H:i') ?? '—' }}
                                </td>
                                <td>
                                    <span class="badge
                                        @if($absensi->status === 'hadir') badge-success
                                        @elseif($absensi->status === 'telat') badge-warning
                                        @else badge-danger
                                        @endif">
                                        {{ ucfirst($absensi->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-cell">Belum ada data absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="ui-card ui-card-pad lg:col-span-2">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="section-label mb-0">Riwayat Gaji Bersih</p>
                    <a href="{{ route('karyawan.slip-gaji.index') }}" class="text-xs text-primary font-medium hover:underline">
                        Slip
                    </a>
                </div>

                @if($riwayatSlip->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($riwayatSlip as $slip)
                            <a href="{{ route('karyawan.slip-gaji.show', $slip) }}"
                                class="flex items-center justify-between gap-3 rounded-lg border border-hairline px-3 py-2.5 hover:bg-canvas-parchment transition-colors">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-ink truncate">
                                        {{ $slip->payrollPeriod->label }}
                                    </p>
                                    <p class="text-[11px] text-ink-muted-48">Gaji bersih</p>
                                </div>
                                <p class="text-sm font-semibold text-ink tabular-nums shrink-0">
                                    {{ $slip->gaji_bersih_format }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-ink-muted-48 py-6 text-center">Belum ada riwayat slip final.</p>
                @endif
            </div>
        </div>
    @endif
@endsection
