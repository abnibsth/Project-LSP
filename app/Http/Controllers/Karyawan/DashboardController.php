<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Slipgaji;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * DashboardController (Karyawan) — halaman utama karyawan setelah login.
 *
 * FUNGSI HALAMAN INI:
 * Menjadi "home" harian karyawan, bukan cuma angka ringkas.
 * Isi data yang dikirim ke view:
 * - Profil singkat (dari relasi user->employee)
 * - Status absensi hari ini (check-in / check-out)
 * - Rekap kehadiran bulan ini (hadir / telat / alpha)
 * - Ringkasan gaji (terakhir, rata-rata, total YTD)
 * - Breakdown slip gaji terakhir (penghasilan & potongan)
 * - Absensi terbaru + aksi cepat
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        // Auth::user() = user yang sedang login sekarang
        $user = Auth::user();

        // Relasi employee: data karyawan (NIK, jabatan, departemen, dll)
        // Bisa null kalau akun belum di-link ke data karyawan
        $employee = $user->employee;

        // Default kosong — diisi hanya jika employee ada
        $absensiHariIni = null;
        $slipTerakhir = null;
        $riwayatSlip = collect();
        $absensiTerbaru = collect();
        $rekapAbsensi = ['hadir' => 0, 'telat' => 0, 'alpha' => 0];
        $rataRataGajiBersih = 0;
        $totalGajiYtd = 0;

        if ($employee) {
            // --------------------------------------------------
            // 1) Absensi hari ini
            // whereDate('tanggal', today()) = filter baris yang tanggalnya = hari ini
            // --------------------------------------------------
            $absensiHariIni = Absensi::query()
                ->where('employee_id', $employee->id)
                ->whereDate('tanggal', today())
                ->first();

            // Query dasar slip FINAL saja.
            // Karyawan tidak boleh lihat slip draft (belum difinalisasi admin).
            $slipFinalQuery = Slipgaji::query()
                ->where('employee_id', $employee->id)
                ->whereHas('payrollPeriod', fn ($q) => $q->where('status', 'final'));

            // --------------------------------------------------
            // 2) Slip gaji terakhir + rincian komponen
            // with([...]) = eager load biar tidak N+1 query di Blade
            // --------------------------------------------------
            $slipTerakhir = (clone $slipFinalQuery)
                ->with(['payrollPeriod', 'components'])
                ->latest('id')
                ->first();

            // --------------------------------------------------
            // 3) Statistik gaji (rata-rata & total tahun berjalan / YTD)
            // avg() & sum() dijalankan di database, bukan di PHP loop
            // --------------------------------------------------
            $rataRataGajiBersih = (float) (clone $slipFinalQuery)->avg('gaji_bersih');

            // YTD = Year To Date → total gaji bersih di tahun berjalan
            $totalGajiYtd = (float) Slipgaji::query()
                ->where('employee_id', $employee->id)
                ->whereHas('payrollPeriod', function ($q) {
                    $q->where('status', 'final')
                        ->where('tahun', now()->year);
                })
                ->sum('gaji_bersih');

            // --------------------------------------------------
            // 4) Riwayat gaji bersih (6 slip terakhir) untuk mini chart/list
            // --------------------------------------------------
            $riwayatSlip = (clone $slipFinalQuery)
                ->with('payrollPeriod')
                ->latest('id')
                ->limit(6)
                ->get();

            // --------------------------------------------------
            // 5) Rekap absensi bulan berjalan
            // Satu query + groupBy status lebih hemat daripada 3x count()
            // --------------------------------------------------
            $counts = Absensi::query()
                ->where('employee_id', $employee->id)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $rekapAbsensi['hadir'] = (int) ($counts['hadir'] ?? 0);
            $rekapAbsensi['telat'] = (int) ($counts['telat'] ?? 0);
            $rekapAbsensi['alpha'] = (int) ($counts['alpha'] ?? 0);

            // --------------------------------------------------
            // 6) 6 absensi terbaru (untuk mengisi whitespace dashboard)
            // --------------------------------------------------
            $absensiTerbaru = Absensi::query()
                ->where('employee_id', $employee->id)
                ->latest('tanggal')
                ->limit(6)
                ->get();
        }

        // compact() mengirim variabel ke view Blade dengan nama yang sama
        return view('karyawan.dashboard', compact(
            'employee',
            'absensiHariIni',
            'slipTerakhir',
            'riwayatSlip',
            'absensiTerbaru',
            'rekapAbsensi',
            'rataRataGajiBersih',
            'totalGajiYtd',
        ));
    }
}
