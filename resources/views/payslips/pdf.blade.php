<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji — {{ $payslip->employee->nama }} — {{ $payslip->payrollPeriod->label }}</title>
    <style>
        /* CSS untuk tampilan PDF slip gaji */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; }

        .header {
            background: #1e40af; /* Warna biru perusahaan */
            color: white;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .company-name { font-size: 18px; font-weight: bold; }
        .slip-title { font-size: 10px; opacity: 0.8; margin-top: 2px; }
        .periode-label {
            text-align: right;
            font-size: 12px;
            font-weight: bold;
        }
        .karyawan-info {
            padding: 16px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .karyawan-info table { width: 100%; }
        .karyawan-info td { padding: 3px 8px; }
        .karyawan-info td:first-child { font-weight: bold; width: 140px; color: #64748b; }

        .gaji-section { padding: 16px 24px; }
        .gaji-section h3 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .gaji-table { width: 100%; border-collapse: collapse; }
        .gaji-table td { padding: 4px 0; }
        .gaji-table td:last-child { text-align: right; }
        .gaji-table .total-row td {
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-weight: bold;
        }
        .tunjangan { color: #16a34a; } /* Hijau untuk tunjangan */
        .potongan { color: #dc2626; }  /* Merah untuk potongan */

        .gaji-bersih-box {
            margin: 16px 24px;
            background: #1e40af;
            color: white;
            padding: 14px 20px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .gaji-bersih-label { font-size: 12px; }
        .gaji-bersih-nilai { font-size: 20px; font-weight: bold; }

        .footer {
            margin: 12px 24px;
            display: flex;
            justify-content: space-between;
            color: #94a3b8;
            font-size: 9px;
        }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    </style>
</head>
<body>

    {{-- Header Slip Gaji --}}
    <div class="header">
        <div>
            <div class="company-name">PT Nikel Indonesia</div>
            <div class="slip-title">Slip Gaji Karyawan</div>
        </div>
        <div class="periode-label">
            {{ strtoupper($payslip->payrollPeriod->label) }}
        </div>
    </div>

    {{-- Data Karyawan --}}
    <div class="karyawan-info">
        <table>
            <tr>
                <td>Nama Karyawan</td>
                <td>: {{ $payslip->employee->nama }}</td>
                <td>Jabatan</td>
                <td>: {{ $payslip->employee->jabatan }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>: {{ $payslip->employee->nik }}</td>
                <td>Departemen</td>
                <td>: {{ $payslip->employee->departemen }}</td>
            </tr>
            <tr>
                <td>No. Rekening</td>
                <td>: {{ $payslip->employee->nama_bank }} — {{ $payslip->employee->no_rekening ?? '-' }}</td>
                <td>Status</td>
                <td>: {{ ucfirst($payslip->employee->status_kerja) }}</td>
            </tr>
        </table>
    </div>

    {{-- Rincian Gaji dalam 2 kolom --}}
    <div class="gaji-section">
        <div class="two-col">
            {{-- Kolom Kiri: Pendapatan --}}
            <div>
                <h3>Pendapatan</h3>
                <table class="gaji-table">
                    <tr>
                        <td>Gaji Pokok</td>
                        <td>Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}</td>
                    </tr>
                    @foreach($payslip->components->where('tipe', 'tunjangan') as $komponen)
                        <tr class="tunjangan">
                            <td>{{ $komponen->nama_komponen }}</td>
                            <td>Rp {{ number_format($komponen->nilai, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td>Total Pendapatan</td>
                        <td>Rp {{ number_format($payslip->gaji_bruto, 0, ',', '.') }}</td>
                    </tr>
                </table>

                {{-- Info Kehadiran --}}
                @if($payslip->detail_json)
                    <h3 style="margin-top:12px;">Kehadiran</h3>
                    <table class="gaji-table">
                        <tr><td>Hari Hadir</td><td>{{ $payslip->detail_json['total_hadir'] ?? 0 }} hari</td></tr>
                        <tr><td>Hari Telat</td><td>{{ $payslip->detail_json['total_telat'] ?? 0 }} hari</td></tr>
                        <tr><td>Hari Alpha</td><td>{{ $payslip->detail_json['total_alpha'] ?? 0 }} hari</td></tr>
                        <tr><td>Total Telat</td><td>{{ $payslip->detail_json['total_menit_telat'] ?? 0 }} menit</td></tr>
                    </table>
                @endif
            </div>

            {{-- Kolom Kanan: Potongan --}}
            <div>
                <h3>Potongan</h3>
                <table class="gaji-table">
                    @foreach($payslip->components->where('tipe', 'potongan') as $komponen)
                        <tr class="potongan">
                            <td>{{ $komponen->nama_komponen }}</td>
                            <td>Rp {{ number_format($komponen->nilai, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @if($payslip->potongan_pajak > 0)
                        <tr class="potongan">
                            <td>Potongan Pajak (PPh 21)</td>
                            <td>Rp {{ number_format($payslip->potongan_pajak, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($payslip->potongan_absensi > 0)
                        <tr class="potongan">
                            <td>Potongan Kehadiran</td>
                            <td>Rp {{ number_format($payslip->potongan_absensi, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="total-row potongan">
                        <td>Total Potongan</td>
                        <td>Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Gaji Bersih (Take-Home Pay) --}}
    <div class="gaji-bersih-box">
        <div class="gaji-bersih-label">💰 Gaji Bersih (Take-Home Pay)</div>
        <div class="gaji-bersih-nilai">Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}</div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div>Dokumen ini dibuat secara otomatis oleh Sistem Penggajian PT Nikel Indonesia</div>
        <div>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</div>
    </div>

</body>
</html>
