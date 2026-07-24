<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Periodepayrol;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ReportController (Admin) — laporan payroll dan absensi.
 *
 * Admin bisa melihat:
 * - Rekap payroll per bulan (total gaji semua karyawan)
 * - Rekap absensi per karyawan/departemen
 *
 * Dan mengexport ke Excel/PDF (butuh package maatwebsite/excel & dompdf).
 */
class ReportController extends Controller
{
    /**
     * Tampilkan laporan rekap payroll bulanan.
     */
    public function payroll(Request $request): View
    {
        $bulan = $request->integer('bulan', now()->month);
        $tahun = $request->integer('tahun', now()->year);

        $periode = Periodepayrol::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with(['payslips.employee'])
            ->first();

        // Hitung total pengeluaran gaji bulan ini
        $totalGajiBruto = $periode?->payslips->sum('gaji_bruto') ?? 0;
        $totalPotongan = $periode?->payslips->sum('total_potongan') ?? 0;
        $totalGajiBersih = $periode?->payslips->sum('gaji_bersih') ?? 0;

        return view('admin.reports.payroll', compact(
            'periode', 'bulan', 'tahun',
            'totalGajiBruto', 'totalPotongan', 'totalGajiBersih'
        ));
    }

    /**
     * Tampilkan laporan rekap absensi.
     */
    public function absensi(Request $request): View
    {
        $bulan = $request->integer('bulan', now()->month);
        $tahun = $request->integer('tahun', now()->year);

        $employees = Karyawan::aktif()
            ->withCount([
                'attendances as total_hadir' => fn ($q) => $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'hadir'),
                'attendances as total_telat' => fn ($q) => $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'telat'),
                'attendances as total_alpha' => fn ($q) => $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'alpha'),
            ])
            ->orderBy('nama')
            ->get();

        return view('admin.reports.absensi', compact('employees', 'bulan', 'tahun'));
    }

    /**
     * Export laporan payroll ke PDF.
     * (Implementasi lengkap setelah install barryvdh/laravel-dompdf)
     */
    public function exportPayroll(Request $request)
    {
        //  Implementasi export PDF payroll
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }

    /**
     * Export laporan absensi ke Excel.
     * (Implementasi lengkap setelah install maatwebsite/excel)
     */
    public function exportAbsensi(Request $request)
    {
        // Implementasi export Excel absensi
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }
}
