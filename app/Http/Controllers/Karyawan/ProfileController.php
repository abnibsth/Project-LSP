<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ProfileController (Karyawan) — karyawan lihat dan update data pribadi sendiri.
 *
 * Karyawan bisa update:
 * - Nomor rekening (untuk transfer gaji)
 * - Alamat
 * - Nomor telepon
 *
 * Yang TIDAK bisa diubah karyawan: nama, jabatan, gaji pokok, NIK
 * (hanya Admin/HRD yang bisa ubah itu).
 */
class ProfileController extends Controller
{
    /**
     * Tampilkan form profil karyawan.
     */
    public function edit(): View
    {
        $employee = Auth::user()->employee;

        return view('karyawan.profile', compact('employee'));
    }

    /**
     * Simpan perubahan data pribadi karyawan.
     */
    public function update(Request $request): RedirectResponse
    {
        $employee = Auth::user()->employee;

        $validated = $request->validate([
            'no_rekening' => ['nullable', 'string', 'max:30'],
            'nama_bank' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $employee->update($validated);

        return back()->with('success', 'Data pribadi berhasil diperbarui.');
    }
}
