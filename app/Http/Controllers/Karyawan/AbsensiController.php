<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AttendanceRule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * AttendanceController (Karyawan) — check-in dan check-out harian karyawan.
 *
 * Alur:
 * 1. Karyawan buka halaman absensi
 * 2. Klik "Check-In" → sistem catat waktu + IP address + tentukan status
 * 3. Klik "Check-Out" → sistem catat waktu pulang
 *
 * Penentuan status:
 * - Hadir  : waktu check-in ≤ (jam_masuk + toleransi)
 * - Telat  : waktu check-in > (jam_masuk + toleransi)
 * - Alpha  : tidak ada check-in hingga akhir hari
 */
class AbsensiController extends Controller
{
    /**
     * Tampilkan halaman absensi karyawan.
     * Berisi tombol check-in/out dan rekap absensi bulan ini.
     */
    public function index(Request $request): View
    {
        $employee = Auth::user()->employee;
        $bulan = $request->integer('bulan', now()->month);
        $tahun = $request->integer('tahun', now()->year);

        // Absensi hari ini
        $absensiHariIni = Absensi::where('employee_id', $employee->id)
            ->whereDate('tanggal', today())
            ->first();

        // Rekap absensi bulan yang dipilih
        $rekapAbsensi = Absensi::where('employee_id', $employee->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $rule = AttendanceRule::berlaku();

        return view('karyawan.absesni', compact('absensiHariIni', 'rekapAbsensi', 'rule', 'bulan', 'tahun'));
    }

    /**
     * Proses check-in karyawan.
     *
     * Langkah:
     * 1. Pastikan belum pernah check-in hari ini
     * 2. Catat waktu sekarang
     * 3. Hitung apakah telat atau hadir
     * 4. Simpan record absensi
     */
    public function checkIn(Request $request): RedirectResponse
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return back()->with('error', 'Data karyawan tidak ditemukan. Hubungi HRD.');
        }

        // Cek apakah sudah check-in hari ini
        $sudahCheckIn = Absensi::where('employee_id', $employee->id)
            ->whereDate('tanggal', today())
            ->exists();

        if ($sudahCheckIn) {
            return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        $rule = AttendanceRule::berlaku();
        $sekarang = Carbon::now();

        // Hitung menit terlambat
        $batasCheckin = Carbon::parse(today()->format('Y-m-d').' '.$rule->jam_masuk)
            ->addMinutes($rule->toleransi_menit);

        $menitTerlambat = 0;
        $status = 'hadir';

        if ($sekarang->greaterThan($batasCheckin)) {
            $status = 'telat';
            $jamMasuk = Carbon::parse(today()->format('Y-m-d').' '.$rule->jam_masuk);
            $menitTerlambat = (int) $jamMasuk->diffInMinutes($sekarang);
        }

        Absensi::create([
            'employee_id' => $employee->id,
            'tanggal' => today(),
            'waktu_checkin' => $sekarang,
            'status' => $status,
            'menit_terlambat' => $menitTerlambat,
            'ip_address' => $request->ip(),
            'is_koreksi' => false,
        ]);

        $pesanStatus = $status === 'hadir'
            ? 'Check-in berhasil! Status: Hadir ✅'
            : "Check-in berhasil! Status: Telat ({$menitTerlambat} menit) ⚠️";

        return back()->with('success', $pesanStatus);
    }

    /**
     * Proses check-out karyawan.
     */
    public function checkOut(Request $request): RedirectResponse
    {
        $employee = Auth::user()->employee;

        $absensiHariIni = Absensi::where('employee_id', $employee->id)
            ->whereDate('tanggal', today())
            ->first();

        if (! $absensiHariIni) {
            return back()->with('error', 'Anda belum melakukan check-in hari ini.');
        }

        if ($absensiHariIni->waktu_checkout) {
            return back()->with('error', 'Anda sudah melakukan check-out hari ini.');
        }

        $absensiHariIni->update([
            'waktu_checkout' => now(),
        ]);

        return back()->with('success', 'Check-out berhasil! Sampai jumpa besok 👋');
    }
}
