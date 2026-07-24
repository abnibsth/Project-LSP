<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Aturanabsen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AttendanceController (Admin) — lihat & koreksi absensi karyawan.
 *
 * Admin bisa:
 * - Lihat rekap absensi semua karyawan (filter bulan/departemen)
 * - Koreksi data absensi karyawan yang salah/lupa check-in
 */
class Rekapabsen extends Controller
{
    /**
     * Tampilkan daftar rekap absensi semua karyawan.
     * Bisa difilter: bulan, tahun, departemen, karyawan tertentu.
     */
    public function index(Request $request): View
    {
        // Default range: tanggal 1 bulan ini s/d hari ini
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());

        $query = Absensi::with('employee')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        // Filter berdasarkan karyawan tertentu
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter berdasarkan status absensi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

        // Data untuk dropdown filter
        $employees = Karyawan::aktif()->orderBy('nama')->get();

        return view('admin.rekap-absen.index', compact(
            'attendances', 'employees', 'tanggalMulai', 'tanggalSelesai',
        ));
    }

    /**
     * Tampilkan form koreksi absensi karyawan.
     * Dipakai saat karyawan lupa check-in atau ada kesalahan data.
     */
    public function editKoreksi(Absensi $attendance): View
    {
        $attendance->load('employee');

        return view('admin.rekap-absen.koreksi', compact('attendance'));
    }

    /**
     * Simpan koreksi absensi yang dilakukan admin.
     * Sistem akan recalculate status dan menit terlambat berdasarkan data baru.
     */
    public function updateKoreksi(Request $request, Absensi $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'waktu_checkin' => ['nullable', 'date_format:H:i'],
            'waktu_checkout' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:hadir,telat,alpha'],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);

        // Hitung ulang menit terlambat jika ada waktu check-in
        $menitTerlambat = 0;
        if ($validated['status'] === 'telat' && $validated['waktu_checkin']) {
            $rule = Aturanabsen::berlaku();
            $jamMasuk = Carbon::parse($attendance->tanggal->format('Y-m-d').' '.$rule->jam_masuk);
            $waktuCheckin = Carbon::parse($attendance->tanggal->format('Y-m-d').' '.$validated['waktu_checkin']);
            $menitTerlambat = max(0, $jamMasuk->diffInMinutes($waktuCheckin, false));
        }

        // Konversi waktu ke format datetime lengkap
        $waktuCheckin = $validated['waktu_checkin']
            ? Carbon::parse($attendance->tanggal->format('Y-m-d').' '.$validated['waktu_checkin'])
            : null;

        $waktuCheckout = $validated['waktu_checkout']
            ? Carbon::parse($attendance->tanggal->format('Y-m-d').' '.$validated['waktu_checkout'])
            : null;

        $attendance->update([
            'waktu_checkin' => $waktuCheckin,
            'waktu_checkout' => $waktuCheckout,
            'status' => $validated['status'],
            'menit_terlambat' => $menitTerlambat,
            'keterangan' => $validated['keterangan'],
            'is_koreksi' => true,
        ]);

        return redirect()->route('admin.absensi.index')
            ->with('success', 'Absensi berhasil dikoreksi.');
    }
}
