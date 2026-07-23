<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * PayslipController (Admin) — kelola slip gaji dari sisi Admin/HRD.
 *
 * Admin bisa:
 * - Lihat semua slip gaji semua karyawan
 * - Lihat detail slip gaji satu karyawan
 * - Download slip gaji sebagai PDF
 * - Update potongan pajak (input manual HRD) — FR-04 di PRD
 */
class Slipgaji extends Controller
{
    /**
     * Daftar semua slip gaji (bisa difilter per periode/karyawan).
     */
    public function index(Request $request): View
    {
        $query = Payslip::with(['employee', 'payrollPeriod'])
            ->whereHas('payrollPeriod', fn ($q) => $q->where('status', 'final'));

        if ($request->filled('payroll_period_id')) {
            $query->where('payroll_period_id', $request->payroll_period_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $payslips = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.slip-gaji.index', compact('payslips'));
    }

    /**
     * Detail satu slip gaji karyawan.
     */
    public function show(Payslip $payslip): View
    {
        $payslip->load(['employee', 'payrollPeriod', 'components']);

        return view('admin.slip-gaji.show', compact('payslip'));
    }

    /**
     * Download slip gaji sebagai file PDF.
     */
    public function download(Payslip $payslip): Response
    {
        $payslip->load(['employee', 'payrollPeriod', 'components']);

        $pdf = Pdf::loadView('payslips.pdf', compact('payslip'))
            ->setPaper('a4', 'portrait')
            // DPI & remote: bantu render lebih stabil di DomPDF
            ->setOption('dpi', 96)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $namaFile = 'slip-gaji-'.$payslip->employee->nik.'-'.$payslip->payrollPeriod->label.'.pdf';
        $namaFile = str_replace(' ', '-', strtolower($namaFile));

        return $pdf->download($namaFile);
    }

    /**
     * Update potongan pajak (PPh 21) yang diinput manual oleh HRD.
     *
     * Sesuai PRD FR-04: "Admin/HRD harus dapat menginput nominal Potongan Pajak
     * secara manual per karyawan per periode"
     *
     * Setelah update, sistem recalculate total_potongan & gaji_bersih.
     */
    public function updatePotonganPajak(Request $request, Payslip $payslip): RedirectResponse
    {
        // Pastikan periode belum final
        if ($payslip->payrollPeriod->isFinal()) {
            return back()->with('error', 'Tidak bisa mengubah potongan pajak. Periode payroll sudah final.');
        }

        $validated = $request->validate([
            'potongan_pajak' => ['required', 'numeric', 'min:0'],
        ]);

        $potonganPajak = (float) $validated['potongan_pajak'];

        // Recalculate total potongan & gaji bersih
        $totalPotongan = (float) $payslip->potongan_absensi + $potonganPajak + (float) $payslip->total_potongan_lain;
        $gajiBersih = max(0, (float) $payslip->gaji_bruto - $totalPotongan);

        $payslip->update([
            'potongan_pajak' => $potonganPajak,
            'total_potongan' => $totalPotongan,
            'gaji_bersih' => $gajiBersih,
        ]);

        return back()->with('success', 'Potongan pajak berhasil diperbarui. Gaji bersih sudah dihitung ulang.');
    }
}
