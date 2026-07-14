<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Services\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * PayrollController (Admin) — kelola proses payroll bulanan.
 *
 * Alur penggunaan:
 * 1. Admin buka /admin/payroll → lihat daftar periode yang sudah ada
 * 2. Klik "Buat Periode Baru" → isi bulan & tahun
 * 3. Klik "Proses Payroll" → sistem hitung gaji semua karyawan
 * 4. Admin review hasilnya, input potongan pajak per karyawan jika perlu
 * 5. Klik "Finalisasi" → data dikunci, slip gaji tersedia untuk karyawan
 */
class PayrollController extends Controller
{
    public function __construct(
        private readonly PayrollService $payrollService
    ) {}

    /**
     * Tampilkan daftar semua periode payroll.
     */
    public function index(): View
    {
        $periodes = PayrollPeriod::with(['payslips', 'finalizedBy'])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->paginate(12);

        return view('admin.payroll.index', compact('periodes'));
    }

    /**
     * Form buat periode payroll baru.
     */
    public function create(): View
    {
        return view('admin.payroll.create');
    }

    /**
     * Simpan periode payroll baru (status: draft).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2020'],
        ]);

        // Cek apakah periode ini sudah ada
        $existing = PayrollPeriod::where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->first();

        if ($existing) {
            return back()->withErrors(['bulan' => 'Periode payroll untuk bulan & tahun tersebut sudah ada.']);
        }

        $periode = PayrollPeriod::create($validated);

        return redirect()->route('admin.payroll.show', $periode)
            ->with('success', 'Periode payroll berhasil dibuat. Klik "Proses Payroll" untuk mulai hitung gaji.');
    }

    /**
     * Tampilkan detail periode payroll beserta daftar slip gaji karyawan.
     */
    public function show(PayrollPeriod $payrollPeriod): View
    {
        $payrollPeriod->load(['payslips.employee', 'payslips.components']);

        return view('admin.payroll.show', compact('payrollPeriod'));
    }

    /**
     * Jalankan proses hitung gaji (payroll run).
     * Hanya bisa dilakukan jika status masih 'draft'.
     */
    public function proses(PayrollPeriod $payrollPeriod): RedirectResponse
    {
        if ($payrollPeriod->isFinal()) {
            return back()->with('error', 'Periode payroll ini sudah final dan tidak bisa diproses ulang.');
        }

        $hasil = $this->payrollService->prosesPayroll($payrollPeriod);

        return redirect()->route('admin.payroll.show', $payrollPeriod)
            ->with('success', "Payroll berhasil diproses! Berhasil: {$hasil['berhasil']} karyawan. Gagal: {$hasil['gagal']} karyawan.");
    }

    /**
     * Finalisasi periode payroll — kunci data agar tidak bisa diubah.
     * Setelah finalisasi, slip gaji bisa dilihat oleh karyawan.
     */
    public function finalisasi(PayrollPeriod $payrollPeriod): RedirectResponse
    {
        if ($payrollPeriod->isFinal()) {
            return back()->with('error', 'Periode ini sudah final.');
        }

        // Pastikan sudah ada slip gaji yang dibuat
        if ($payrollPeriod->payslips()->count() === 0) {
            return back()->with('error', 'Belum ada data gaji. Jalankan proses payroll terlebih dahulu.');
        }

        $payrollPeriod->update([
            'status' => 'final',
            'tanggal_finalisasi' => now(),
            'finalized_by' => Auth::id(),
        ]);

        return redirect()->route('admin.payroll.show', $payrollPeriod)
            ->with('success', 'Payroll berhasil difinalisasi! Slip gaji sudah bisa diakses oleh karyawan.');
    }
}
