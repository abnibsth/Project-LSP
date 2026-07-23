<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji — {{ $payslip->employee->nama }} — {{ $payslip->payrollPeriod->label }}</title>
    {{--
        pdf.css via path file lokal (public_path).
        DomPDF: hindari width:100% + padding di elemen yang sama.
    --}}
    <link rel="stylesheet" href="{{ public_path('pdf.css') }}">
</head>
<body>
    {{--
        FUNGSI TEMPLATE INI:
        Layout slip gaji PDF (A4 portrait) untuk admin & karyawan.

        Struktur:
        1. Header (logo + judul + periode)
        2. Meta identitas perusahaan & karyawan
        3. Dua kolom: rekap absensi harian | rincian gaji
        4. Catatan + tanda tangan

        Anti potong kanan (DomPDF):
        - Wrapper .page (border) + .page-inner (padding) terpisah
        - table-layout: fixed
        - angka tanpa white-space:nowrap
    --}}

    @php
        $bulan = $payslip->payrollPeriod->bulan;
        $tahun = $payslip->payrollPeriod->tahun;

        // Absensi bulan slip — keyBy tanggal biar lookup cepat
        $attendances = \App\Models\Absensi::where('employee_id', $payslip->employee_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->keyBy(fn ($item) => $item->tanggal->format('Y-m-d'));

        $startOfMonth = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $datesInMonth = [];

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $datesInMonth[] = $date->copy();
        }

        // Carbon dayOfWeek: 0 Minggu … 6 Sabtu
        $indonesianDays = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        $tunjangan = $payslip->components->where('tipe', 'tunjangan');
        $potonganKomponen = $payslip->components->where('tipe', 'potongan');

        // Helper format rupiah singkat di PDF
        $rp = fn ($n) => 'Rp '.number_format((float) $n, 0, ',', '.');
    @endphp

    <div class="page">
        <div class="page-inner">

            {{-- HEADER --}}
            <table class="header-table">
                <tr>
                    <td class="header-brand">
                        <img src="{{ public_path('logo.png') }}" alt="Logo PT Nikel Indonesia">
                        <div class="header-brand-name">PT NIKEL INDONESIA</div>
                    </td>
                    <td class="header-title-wrap">
                        <div class="header-title">SLIP GAJI BULANAN</div>
                        <div class="header-period">{{ strtoupper($payslip->payrollPeriod->label) }}</div>
                    </td>
                </tr>
            </table>

            {{-- META --}}
            <table class="meta-table">
                <tr>
                    <td class="label">PERUSAHAAN</td>
                    <td class="colon">:</td>
                    <td class="val">PT NIKEL INDONESIA</td>
                    <td class="label">NAMA</td>
                    <td class="colon">:</td>
                    <td class="val">{{ strtoupper($payslip->employee->nama) }}</td>
                </tr>
                <tr>
                    <td class="label">REKENING</td>
                    <td class="colon">:</td>
                    <td class="val">
                        {{ strtoupper($payslip->employee->nama_bank ?? '-') }}
                        - {{ $payslip->employee->no_rekening ?? '-' }}
                    </td>
                    <td class="label">ALAMAT</td>
                    <td class="colon">:</td>
                    <td class="val">{{ strtoupper($payslip->employee->alamat ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label">STATUS</td>
                    <td class="colon">:</td>
                    <td class="val">{{ strtoupper($payslip->employee->status_kerja) }}</td>
                    <td class="label">NIK</td>
                    <td class="colon">:</td>
                    <td class="val">{{ $payslip->employee->nik }}</td>
                </tr>
                <tr>
                    <td class="label">JABATAN</td>
                    <td class="colon">:</td>
                    <td class="val">{{ strtoupper($payslip->employee->jabatan) }}</td>
                    <td class="label">DEPT</td>
                    <td class="colon">:</td>
                    <td class="val">{{ strtoupper($payslip->employee->departemen) }}</td>
                </tr>
            </table>

            {{-- BADAN: Absensi | Gaji --}}
            <table class="layout-table">
                <tr>
                    <td class="col-attendance">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th style="width: 9%;" rowspan="2">TGL</th>
                                    <th style="width: 12%;" rowspan="2">HARI</th>
                                    <th colspan="2">JAM KERJA</th>
                                    <th style="width: 16%;" rowspan="2">STATUS</th>
                                    <th style="width: 11%;" rowspan="2">JAM</th>
                                </tr>
                                <tr>
                                    <th style="width: 26%;">MASUK</th>
                                    <th style="width: 26%;">PULANG</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datesInMonth as $date)
                                    @php
                                        $dateStr = $date->toDateString();
                                        $attendance = $attendances->get($dateStr);

                                        $in = '-';
                                        $out = '-';
                                        $statusKey = 'L';
                                        $sum = 0.0;

                                        if ($date->isWeekend()) {
                                            $statusKey = 'L';
                                        } elseif ($attendance) {
                                            if ($attendance->status === 'alpha') {
                                                $statusKey = 'A';
                                            } else {
                                                $statusKey = $attendance->status === 'telat' ? 'T' : 'H';
                                                $in = $attendance->waktu_checkin
                                                    ? \Carbon\Carbon::parse($attendance->waktu_checkin)->format('H:i')
                                                    : '-';
                                                $out = $attendance->waktu_checkout
                                                    ? \Carbon\Carbon::parse($attendance->waktu_checkout)->format('H:i')
                                                    : '-';

                                                // Jam efektif = durasi - 1 jam istirahat
                                                if ($attendance->waktu_checkin && $attendance->waktu_checkout) {
                                                    $checkin = \Carbon\Carbon::parse($attendance->waktu_checkin);
                                                    $checkout = \Carbon\Carbon::parse($attendance->waktu_checkout);
                                                    $diff = $checkin->diffInMinutes($checkout);
                                                    $sum = round(max(0, ($diff - 60) / 60), 1);
                                                }
                                            }
                                        } else {
                                            $statusKey = 'A';
                                        }

                                        $statusMap = [
                                            'H' => 'HADIR',
                                            'T' => 'TELAT',
                                            'A' => 'ALPA',
                                            'L' => 'LIBUR',
                                        ];
                                        $displayStatus = $statusMap[$statusKey] ?? $statusKey;
                                        $dayName = $indonesianDays[$date->dayOfWeek];
                                    @endphp
                                    <tr class="{{ $date->isWeekend() ? 'weekend-row' : '' }} {{ $statusKey === 'A' ? 'alpha-row' : '' }}">
                                        <td>{{ $date->format('d') }}</td>
                                        <td>{{ $dayName }}</td>
                                        <td>{{ $in }}</td>
                                        <td>{{ $out }}</td>
                                        <td style="font-weight: bold;">{{ $displayStatus }}</td>
                                        <td>{{ $sum > 0 ? number_format($sum, 1) : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>

                    <td class="col-salary">
                        {{-- Padding aman: nested table, bukan padding di td width 50% --}}
                        <table class="salary-pad">
                            <tr>
                                <td>
                                    <div class="salary-group-title first">1. Pendapatan</div>
                                    <table class="salary-table">
                                        <tr>
                                            <td class="desc">Gaji Pokok</td>
                                            <td class="amount">{{ $rp($payslip->gaji_pokok) }}</td>
                                        </tr>
                                        @foreach($tunjangan as $komponen)
                                            <tr>
                                                <td class="desc">{{ $komponen->keterangan ?: $komponen->nama_komponen }}</td>
                                                <td class="amount">{{ $rp($komponen->nilai) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="total-row">
                                            <td class="desc">Total Bruto (A)</td>
                                            <td class="amount">{{ $rp($payslip->gaji_bruto) }}</td>
                                        </tr>
                                    </table>

                                    <div class="salary-group-title">2. Potongan</div>
                                    <table class="salary-table">
                                        @if((float) $payslip->potongan_absensi > 0)
                                            <tr>
                                                <td class="desc">Potongan Kehadiran / Absensi</td>
                                                <td class="amount amount-deduction">{{ $rp($payslip->potongan_absensi) }}</td>
                                            </tr>
                                        @endif
                                        @if((float) $payslip->potongan_pajak > 0)
                                            <tr>
                                                <td class="desc">Potongan Pajak (PPh 21)</td>
                                                <td class="amount amount-deduction">{{ $rp($payslip->potongan_pajak) }}</td>
                                            </tr>
                                        @endif
                                        @foreach($potonganKomponen as $komponen)
                                            <tr>
                                                <td class="desc">{{ $komponen->keterangan ?: $komponen->nama_komponen }}</td>
                                                <td class="amount amount-deduction">{{ $rp($komponen->nilai) }}</td>
                                            </tr>
                                        @endforeach
                                        @if(
                                            (float) $payslip->potongan_absensi <= 0
                                            && (float) $payslip->potongan_pajak <= 0
                                            && $potonganKomponen->isEmpty()
                                        )
                                            <tr>
                                                <td class="desc" colspan="2" style="color: #6e6e73;">Tidak ada potongan</td>
                                            </tr>
                                        @endif
                                        <tr class="total-row">
                                            <td class="desc">Total Potongan (B)</td>
                                            <td class="amount amount-deduction">{{ $rp($payslip->total_potongan) }}</td>
                                        </tr>
                                    </table>

                                    <div class="salary-group-title">3. Ringkasan</div>
                                    <div class="summary-box">
                                        <table class="summary-table">
                                            <tr>
                                                <td class="s-label">Total Bruto (A)</td>
                                                <td class="s-value">{{ $rp($payslip->gaji_bruto) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="s-label">Total Potongan (B)</td>
                                                <td class="s-value">{{ $rp($payslip->total_potongan) }}</td>
                                            </tr>
                                            <tr class="net">
                                                <td class="s-label">Gaji Bersih / THP (A - B)</td>
                                                <td class="s-value">{{ $rp($payslip->gaji_bersih) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="notes">
                <strong>Catatan:</strong>
                Perusahaan hanya melayani klarifikasi waktu kerja satu hari setelah penerimaan slip gaji.
                Dokumen ini sah dan diterbitkan secara elektronik oleh Sistem Penggajian PT Nikel Indonesia.
            </div>

            <table class="signature-table">
                <tr>
                    <td>Diterima oleh,</td>
                    <td>Hormat Kami,</td>
                </tr>
                <tr>
                    <td class="space"></td>
                    <td class="space"></td>
                </tr>
                <tr>
                    <td class="signature-name">{{ strtoupper($payslip->employee->nama) }}</td>
                    <td class="signature-name">ADMIN HRD</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
