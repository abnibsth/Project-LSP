<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use Illuminate\View\View;

/**
 * DashboardController (Admin) — menampilkan halaman dashboard Admin/HRD.
 *
 * Dashboard ini menampilkan ringkasan/statistik penting:
 * - Total karyawan aktif
 * - Status absensi hari ini
 * - Status periode payroll terbaru
 * - Informasi cepat untuk HRD
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard Admin.
     *
     * Data yang dikirim ke view:
     * - totalKaryawan : jumlah karyawan aktif
     * - hadirHariIni  : berapa yang sudah check-in hari ini
     * - alphaHariIni  : berapa yang alpha hari ini
     * - periodeAktif  : periode payroll terbaru (draft/final)
     */
    public function index(): View
    {
        $today = now()->toDateString();

        // Hitung statistik untuk card ringkasan
        $totalKaryawan = Employee::aktif()->count();

        $hadirHariIni = Attendance::whereDate('tanggal', $today)
            ->whereIn('status', ['hadir', 'telat'])
            ->count();

        $alphaHariIni = $totalKaryawan - Attendance::whereDate('tanggal', $today)->count();
        $alphaHariIni = max(0, $alphaHariIni); // Jangan sampai negatif

        // Ambil periode payroll paling baru
        $periodeAktif = PayrollPeriod::orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'hadirHariIni',
            'alphaHariIni',
            'periodeAktif',
        ));
    }
}
