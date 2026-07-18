<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji — {{ $payslip->employee->nama }} — {{ $payslip->payrollPeriod->label }}</title>
    <link rel="stylesheet" href="{{ public_path('pdf.css') }}">
</head>
<body>

    @php
        $bulan = $payslip->payrollPeriod->bulan;
        $tahun = $payslip->payrollPeriod->tahun;
        
        // Ambil data absensi karyawan pada periode ini
        $attendances = \App\Models\Absensi::where('employee_id', $payslip->employee_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();
            
        // Buat daftar tanggal lengkap untuk bulan tersebut
        $startOfMonth = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $datesInMonth = [];
        
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $datesInMonth[] = $date->copy();
        }
        
        $indonesianDays = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    @endphp

    <div class="border-container">
        
        {{-- Title Section --}}
        <div class="title-section" style="border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 8px;">
            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
                <tr>
                    <td style="width: 50%; text-align: left; vertical-align: middle; border: none; padding: 0;">
                        <img src="{{ public_path('logo.png') }}" style="height: 28px; width: auto; object-fit: contain;">
                        <div style="font-size: 8px; font-weight: bold; margin-top: 2px; color: #1e3a8a;">PT NIKEL INDONESIA</div>
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: middle; border: none; padding: 0;">
                        <h1 style="font-size: 13px; font-weight: bold; margin: 0; padding: 0; letter-spacing: 0.5px;">SLIP GAJI BULANAN</h1>
                        <p style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; margin: 0; padding: 0;">({{ strtoupper($payslip->payrollPeriod->label) }})</p>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Meta Section --}}
        <table class="meta-table">
            <tr>
                <td class="label">PERUSAHAAN</td>
                <td class="colon">:</td>
                <td class="val">PT NIKEL INDONESIA</td>
                <td class="label">NAMA KARYAWAN</td>
                <td class="colon">:</td>
                <td class="val">{{ strtoupper($payslip->employee->nama) }}</td>
            </tr>
            <tr>
                <td class="label">NO. REKENING</td>
                <td class="colon">:</td>
                <td class="val">{{ strtoupper($payslip->employee->nama_bank) }} - {{ $payslip->employee->no_rekening ?? '-' }}</td>
                <td class="label">ALAMAT</td>
                <td class="colon">:</td>
                <td class="val">{{ strtoupper($payslip->employee->alamat ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">STATUS KARYAWAN</td>
                <td class="colon">:</td>
                <td class="val">{{ strtoupper($payslip->employee->status_kerja) }}</td>
                <td class="label">NIK KTP</td>
                <td class="colon">:</td>
                <td class="val">{{ $payslip->employee->nik }}</td>
            </tr>
            <tr>
                <td class="label">JABATAN</td>
                <td class="colon">:</td>
                <td class="val">{{ strtoupper($payslip->employee->jabatan) }}</td>
                <td class="label">DEPARTEMEN</td>
                <td class="colon">:</td>
                <td class="val">{{ strtoupper($payslip->employee->departemen) }}</td>
            </tr>
        </table>

        {{-- Main Layout Table --}}
        <table class="layout-table">
            <tr>
                {{-- Left Column: Attendance --}}
                <td style="width: 50%;">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 10%;">TGL</th>
                                <th rowspan="2" style="width: 15%;">HARI</th>
                                <th colspan="2">Jam Kerja</th>
                                <th rowspan="2" style="width: 20%;">STATUS</th>
                                <th rowspan="2" style="width: 12%;">SUM</th>
                            </tr>
                            <tr>
                                <th style="width: 21%;">IN</th>
                                <th style="width: 21%;">OUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalJam = 0; @endphp
                            @foreach($datesInMonth as $date)
                                @php
                                    $dateStr = $date->toDateString();
                                    
                                    // Cari data absensi (safe memory compare)
                                    $attendance = $attendances->first(function($item) use ($dateStr) {
                                        return $item->tanggal->format('Y-m-d') === $dateStr;
                                    });
                                    
                                    $in = '-';
                                    $out = '-';
                                    $status = '-';
                                    $sum = 0.0;
                                    
                                    if ($date->isWeekend()) {
                                        $status = 'L'; // Libur
                                    }
                                    
                                    if ($attendance) {
                                        $status = strtoupper(substr($attendance->status, 0, 1)); // H, T, A
                                        if ($attendance->status !== 'alpha') {
                                            $in = $attendance->waktu_checkin ? \Carbon\Carbon::parse($attendance->waktu_checkin)->format('H:i') : '-';
                                            $out = $attendance->waktu_checkout ? \Carbon\Carbon::parse($attendance->waktu_checkout)->format('H:i') : '-';
                                            
                                            if ($attendance->waktu_checkin && $attendance->waktu_checkout) {
                                                $checkin = \Carbon\Carbon::parse($attendance->waktu_checkin);
                                                $checkout = \Carbon\Carbon::parse($attendance->waktu_checkout);
                                                $diff = $checkin->diffInMinutes($checkout);
                                                
                                                // Jam kerja dikurangi 1 jam istirahat
                                                $hours = max(0, ($diff - 60) / 60);
                                                $sum = round($hours, 1);
                                                $totalJam += $sum;
                                            }
                                        } else {
                                            $status = 'A'; // Alpha
                                        }
                                    } else {
                                        if (!$date->isWeekend()) {
                                            $status = 'A'; // Weekday tapi tidak absen
                                        }
                                    }
                                    
                                    $statusMap = [
                                        'H' => 'HADIR',
                                        'T' => 'TELAT',
                                        'A' => 'ALPA',
                                        'L' => 'LIBUR'
                                    ];
                                    $displayStatus = $statusMap[$status] ?? $status;
                                    $dayName = $indonesianDays[$date->dayOfWeek];
                                @endphp
                                <tr class="{{ $date->isWeekend() ? 'weekend-row' : '' }} {{ $status === 'A' ? 'alpha-row' : '' }}">
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

                {{-- Right Column: Salary Details --}}
                <td style="width: 50%;" class="salary-column">
                    <div class="salary-group-title">1. BASIS SLIP GAJI</div>
                    <table class="salary-table">
                        <tr>
                            <td>Gaji Pokok (Basic Salary)</td>
                            <td class="amount">Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}</td>
                        </tr>
                        @foreach($payslip->components->where('tipe', 'tunjangan') as $komponen)
                            <tr>
                                <td>{{ $komponen->keterangan ?: $komponen->nama_komponen }}</td>
                                <td class="amount">Rp {{ number_format($komponen->nilai, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </table>

                    <div class="salary-group-title">2. HITUNGAN UPAH BRUTO (IDR)</div>
                    <table class="salary-table">
                        <tr>
                            <td>Gaji Pokok</td>
                            <td class="amount">Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}</td>
                        </tr>
                        @foreach($payslip->components->where('tipe', 'tunjangan') as $komponen)
                            <tr>
                                <td>{{ $komponen->keterangan ?: $komponen->nama_komponen }}</td>
                                <td class="amount">Rp {{ number_format($komponen->nilai, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>Total Pembayaran Bruto</td>
                            <td class="amount">Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <div class="salary-group-title">3. HITUNGAN PAJAK - POTONGAN (Rp)</div>
                    <table class="salary-table">
                        @if($payslip->potongan_absensi > 0)
                            <tr>
                                <td>Potongan Kehadiran / Absensi</td>
                                <td class="amount">Rp {{ number_format($payslip->potongan_absensi, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($payslip->potongan_pajak > 0)
                            <tr>
                                <td>Potongan Pajak (PPh 21)</td>
                                <td class="amount">Rp {{ number_format($payslip->potongan_pajak, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @foreach($payslip->components->where('tipe', 'potongan') as $komponen)
                            <tr>
                                <td>{{ $komponen->keterangan ?: $komponen->nama_komponen }}</td>
                                <td class="amount">Rp {{ number_format($komponen->nilai, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>Total Potongan</td>
                            <td class="amount" style="color: #ef4444;">Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <div class="salary-group-title">4. HITUNGAN SUMMARY (Rp)</div>
                    <div class="summary-box">
                        <div class="summary-row">
                            <div class="summary-label">Total Pendapatan Bruto (A)</div>
                            <div class="summary-value">Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}</div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Total Potongan & Pajak (B)</div>
                            <div class="summary-value">Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}</div>
                        </div>
                        <div class="summary-row" style="font-weight: bold; border-top: 1px solid #000; padding-top: 3px; margin-top: 3px; font-size: 8px;">
                            <div class="summary-label">THP - Gaji Bersih (A - B)</div>
                            <div class="summary-value" style="color: #16a34a;">Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Notes section --}}
        <div class="notes">
            <strong>Catatan:</strong> Perusahaan hanya melayani klarifikasi waktu kerja satu hari setelah penerimaan / penandatanganan slip gaji. Dokumen ini sah dan diterbitkan secara elektronik oleh Sistem Penggajian PT Nikel Indonesia.
        </div>

        {{-- Signatures --}}
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
                <td style="text-decoration: underline; font-weight: bold;">{{ strtoupper($payslip->employee->nama) }}</td>
                <td style="text-decoration: underline; font-weight: bold;">ADMIN HRD</td>
            </tr>
        </table>

    </div>

</body>
</html>
