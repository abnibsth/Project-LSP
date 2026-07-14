<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * PayslipController (Karyawan) — karyawan lihat dan download slip gaji SENDIRI.
 *
 * Keamanan:
 * - Karyawan HANYA bisa melihat slip gaji miliknya sendiri (FR-06 di PRD)
 * - Sistem verifikasi employee_id saat setiap request
 */
class PayslipController extends Controller
{
    /**
     * Daftar slip gaji milik karyawan yang sedang login.
     * Hanya menampilkan slip yang sudah final (sudah disetujui HRD).
     */
    public function index(): View
    {
        $employee = Auth::user()->employee;

        $payslips = Payslip::where('employee_id', $employee->id)
            ->whereHas('payrollPeriod', fn ($q) => $q->where('status', 'final'))
            ->with('payrollPeriod')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('karyawan.payslips.index', compact('payslips'));
    }

    /**
     * Detail satu slip gaji karyawan.
     *
     * Verifikasi: pastikan slip ini memang milik karyawan yang login.
     */
    public function show(Payslip $payslip): View
    {
        $employee = Auth::user()->employee;

        // Keamanan: pastikan slip ini milik karyawan yang sedang login
        abort_if($payslip->employee_id !== $employee->id, 403, 'Anda tidak memiliki akses ke slip gaji ini.');

        $payslip->load(['employee', 'payrollPeriod', 'components']);

        return view('karyawan.payslips.show', compact('payslip'));
    }

    /**
     * Download slip gaji sebagai PDF.
     */
    public function download(Payslip $payslip): Response
    {
        $employee = Auth::user()->employee;

        // Keamanan: pastikan slip ini milik karyawan yang sedang login
        abort_if($payslip->employee_id !== $employee->id, 403, 'Anda tidak memiliki akses ke slip gaji ini.');

        $payslip->load(['employee', 'payrollPeriod', 'components']);

        $pdf = Pdf::loadView('payslips.pdf', compact('payslip'))
            ->setPaper('a4', 'portrait');

        $namaFile = 'slip-gaji-'.$payslip->payrollPeriod->label.'.pdf';
        $namaFile = str_replace(' ', '-', strtolower($namaFile));

        return $pdf->download($namaFile);
    }
}
