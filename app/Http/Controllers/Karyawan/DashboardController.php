<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Payslip;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * DashboardController (Karyawan) — halaman utama karyawan setelah login.
 *
 * Menampilkan:
 * - Gaji terakhir (take-home pay bulan lalu)
 * - Status absensi hari ini (sudah check-in/belum?)
 * - Ringkasan kehadiran bulan ini (hadir, telat, alpha)
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Status absensi hari ini
        $absensiHariIni = null;
        if ($employee) {
            $absensiHariIni = Attendance::where('employee_id', $employee->id)
                ->whereDate('tanggal', today())
                ->first();
        }

        // Slip gaji terakhir yang sudah final
        $slipTerakhir = null;
        if ($employee) {
            $slipTerakhir = Payslip::where('employee_id', $employee->id)
                ->whereHas('payrollPeriod', fn ($q) => $q->where('status', 'final'))
                ->with('payrollPeriod')
                ->latest()
                ->first();
        }

        // Rekap absensi bulan ini
        $rekapAbsensi = ['hadir' => 0, 'telat' => 0, 'alpha' => 0];
        if ($employee) {
            $rekapAbsensi['hadir'] = Attendance::where('employee_id', $employee->id)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->where('status', 'hadir')->count();

            $rekapAbsensi['telat'] = Attendance::where('employee_id', $employee->id)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->where('status', 'telat')->count();

            $rekapAbsensi['alpha'] = Attendance::where('employee_id', $employee->id)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->where('status', 'alpha')->count();
        }

        return view('karyawan.dashboard', compact(
            'employee', 'absensiHariIni', 'slipTerakhir', 'rekapAbsensi'
        ));
    }
}
