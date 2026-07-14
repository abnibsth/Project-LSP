<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AttendanceRuleController (Admin) — kelola aturan absensi perusahaan.
 *
 * Hanya ada SATU halaman: form edit aturan absensi.
 * Admin bisa ubah: jam masuk, jam pulang, toleransi, dan nominal potongan.
 */
class AttendanceRuleController extends Controller
{
    /**
     * Tampilkan form edit aturan absensi.
     * Jika belum ada aturan, buat dengan nilai default.
     */
    public function edit(): View
    {
        $rule = AttendanceRule::berlaku();

        return view('admin.aturan-absensi.edit', compact('rule'));
    }

    /**
     * Simpan perubahan aturan absensi.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jam_masuk' => ['required'],
            'jam_keluar' => ['required'],
            'toleransi_menit' => ['required', 'integer', 'min:0', 'max:120'],
            'potongan_per_alpha' => ['required', 'numeric', 'min:0'],
            'potongan_per_menit_telat' => ['required', 'numeric', 'min:0'],
        ], [
            'toleransi_menit.max' => 'Toleransi maksimal 120 menit.',
        ]);

        $rule = AttendanceRule::berlaku();
        $rule->update($validated);

        return redirect()->route('admin.aturan-absensi.edit')
            ->with('success', 'Aturan absensi berhasil diperbarui.');
    }
}
