<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Aturanabsen;
use App\Models\Karyawan;
use App\Models\Komponengaji;
use App\Models\Komponenslip;
use App\Models\Periodepayrol;
use App\Models\Slipgaji;
use Illuminate\Database\Eloquent\Collection;

/**
 * PayrollService — Service class berisi SEMUA logika perhitungan gaji.
 *
 * Kenapa dibuat service class terpisah?
 * Supaya logika hitung gaji TIDAK tercampur di dalam controller.
 * Controller cukup memanggil service ini, sehingga kode lebih rapi & mudah dites.
 *
 * RUMUS UTAMA:
 *   Gaji Bruto      = Gaji Pokok + Total Tunjangan
 *   Potongan Total  = Potongan Absensi + Potongan Pajak (manual HRD) + Potongan Lain
 *   Gaji Bersih     = Gaji Bruto - Total Potongan
 */
class PayrollService
{
    /**
     * Jalankan proses hitung gaji untuk SEMUA karyawan aktif dalam satu periode.
     *
     * Langkah-langkah:
     * 1. Ambil semua karyawan aktif
     * 2. Untuk setiap karyawan: hitung gajinya menggunakan hitungGajiKaryawan()
     * 3. Simpan hasil ke tabel gaji & komponen_gaji
     *
     * @param  Periodepayrol  $periode  Periode payroll yang akan diproses
     * @return array{berhasil: int, gagal: int} Ringkasan hasil proses
     */
    public function prosesPayroll(Periodepayrol $periode): array
    {
        $karyawanAktif = Karyawan::aktif()->with('attendances')->get();
        $rule = Aturanabsen::berlaku();
        $komponenAktif = Komponengaji::aktif()->get();

        $berhasil = 0;
        $gagal = 0;

        foreach ($karyawanAktif as $karyawan) {
            try {
                $this->hitungGajiKaryawan($karyawan, $periode, $rule, $komponenAktif);
                $berhasil++;
            } catch (\Exception $e) {
                $gagal++;
            }
        }

        return ['berhasil' => $berhasil, 'gagal' => $gagal];
    }

    /**
     * Hitung gaji untuk SATU karyawan dalam satu periode.
     *
     * Jika sudah ada slip gaji untuk periode ini, data akan di-overwrite
     * (kecuali potongan pajak yang sudah diinput HRD — itu tetap dipertahankan).
     *
     * @param  Karyawan  $karyawan  Data karyawan
     * @param  Periodepayrol  $periode  Periode payroll
     * @param  Aturanabsen  $rule  Aturan absensi yang berlaku
     * @param  Collection  $komponenAktif  Daftar komponen gaji aktif
     */
    public function hitungGajiKaryawan(
        Karyawan $karyawan,
        Periodepayrol $periode,
        Aturanabsen $rule,
        $komponenAktif
    ): Slipgaji {
        $gajiPokok = (float) $karyawan->gaji_pokok;

        // =========================================================
        // LANGKAH 1: Hitung semua TUNJANGAN
        // =========================================================
        $rincianTunjangan = [];
        $totalTunjangan = 0.0;

        foreach ($komponenAktif->where('tipe', 'tunjangan') as $komponen) {
            $nilai = $komponen->hitungNilai($gajiPokok);
            $totalTunjangan += $nilai;
            $rincianTunjangan[] = [
                'nama_komponen' => $komponen->nama_komponen,
                'keterangan' => $komponen->keterangan,
                'tipe' => 'tunjangan',
                'nilai' => $nilai,
            ];
        }

        // =========================================================
        // LANGKAH 2: Hitung GAJI BRUTO
        // =========================================================
        $gajiBruto = $gajiPokok + $totalTunjangan;

        // =========================================================
        // LANGKAH 3: Hitung POTONGAN ABSENSI (otomatis dari data absensi)
        // =========================================================
        $absensiKaryawan = Absensi::where('employee_id', $karyawan->id)
            ->whereMonth('tanggal', $periode->bulan)
            ->whereYear('tanggal', $periode->tahun)
            ->get();

        $potonganAbsensi = 0.0;
        $totalMenitTelat = 0;
        $totalAlpha = 0;

        foreach ($absensiKaryawan as $absensi) {
            $potonganAbsensi += $absensi->hitungPotongan($rule);

            if ($absensi->status === 'alpha') {
                $totalAlpha++;
            } elseif ($absensi->status === 'telat') {
                $totalMenitTelat += $absensi->menit_terlambat;
            }
        }

        // =========================================================
        // LANGKAH 4: Ambil POTONGAN LAIN (kasbon, dll)
        // =========================================================
        $rincianPotonganLain = [];
        $totalPotonganLain = 0.0;

        foreach ($komponenAktif->where('tipe', 'potongan') as $komponen) {
            // Potongan lain dengan nilai 0 diabaikan (kasbon diinput per karyawan)
            if ($komponen->nilai > 0) {
                $nilai = $komponen->hitungNilai($gajiPokok);
                $totalPotonganLain += $nilai;
                $rincianPotonganLain[] = [
                    'nama_komponen' => $komponen->nama_komponen,
                    'keterangan' => $komponen->keterangan,
                    'tipe' => 'potongan',
                    'nilai' => $nilai,
                ];
            }
        }

        // =========================================================
        // LANGKAH 5: Pertahankan potongan pajak yang sudah diinput HRD
        // =========================================================
        $slipLama = Slipgaji::where('employee_id', $karyawan->id)
            ->where('payroll_period_id', $periode->id)
            ->first();

        // Jika sudah ada slip sebelumnya, pertahankan potongan pajak yang sudah diinput
        $potonganPajak = $slipLama ? (float) $slipLama->potongan_pajak : 0.0;

        // =========================================================
        // LANGKAH 6: Hitung TOTAL POTONGAN & GAJI BERSIH
        // =========================================================
        $totalPotongan = $potonganAbsensi + $potonganPajak + $totalPotonganLain;
        $gajiBersih = max(0, $gajiBruto - $totalPotongan); // Gaji bersih tidak boleh negatif

        // =========================================================
        // LANGKAH 7: Simpan/Update Slip Gaji di database
        // =========================================================
        $payslip = Slipgaji::updateOrCreate(
            [
                'employee_id' => $karyawan->id,
                'payroll_period_id' => $periode->id,
            ],
            [
                'gaji_pokok' => $gajiPokok,
                'total_tunjangan' => $totalTunjangan,
                'gaji_bruto' => $gajiBruto,
                'potongan_absensi' => $potonganAbsensi,
                'potongan_pajak' => $potonganPajak,
                'total_potongan_lain' => $totalPotonganLain,
                'total_potongan' => $totalPotongan,
                'gaji_bersih' => $gajiBersih,
                'detail_json' => [
                    'total_hari_kerja' => $absensiKaryawan->count(),
                    'total_hadir' => $absensiKaryawan->where('status', 'hadir')->count(),
                    'total_telat' => $absensiKaryawan->where('status', 'telat')->count(),
                    'total_alpha' => $totalAlpha,
                    'total_menit_telat' => $totalMenitTelat,
                ],
            ]
        );

        // =========================================================
        // LANGKAH 8: Simpan Rincian Komponen (untuk tampilan detail)
        // =========================================================
        // Hapus rincian lama dulu
        $payslip->components()->delete();

        // Simpan rincian tunjangan
        foreach ($rincianTunjangan as $rincian) {
            Komponenslip::create(array_merge(['payslip_id' => $payslip->id], $rincian));
        }

        // Simpan rincian potongan lain
        foreach ($rincianPotonganLain as $rincian) {
            Komponenslip::create(array_merge(['payslip_id' => $payslip->id], $rincian));
        }

        // Simpan potongan absensi sebagai komponen jika ada
        if ($potonganAbsensi > 0) {
            Komponenslip::create([
                'payslip_id' => $payslip->id,
                'nama_komponen' => 'Potongan Kehadiran (Alpha: '.$totalAlpha.' hari, Telat: '.$totalMenitTelat.' menit)',
                'tipe' => 'potongan',
                'nilai' => $potonganAbsensi,
            ]);
        }

        // Simpan potongan pajak jika ada
        if ($potonganPajak > 0) {
            Komponenslip::create([
                'payslip_id' => $payslip->id,
                'nama_komponen' => 'Potongan Pajak (PPh 21)',
                'tipe' => 'potongan',
                'nilai' => $potonganPajak,
            ]);
        }

        return $payslip;
    }
}
