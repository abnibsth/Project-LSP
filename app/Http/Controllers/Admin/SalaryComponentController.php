<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SalaryComponentController (Admin) — CRUD untuk komponen gaji.
 *
 * Komponen gaji adalah tunjangan atau potongan yang berlaku untuk semua karyawan.
 * Contoh:
 * - Tunjangan Makan (nominal: Rp 500.000)
 * - Tunjangan Transport (nominal: Rp 300.000)
 * - Tunjangan Jabatan (persentase: 10% dari gaji pokok)
 * - Kasbon (nominal: diinput per karyawan saat payroll)
 */
class SalaryComponentController extends Controller
{
    public function index(): View
    {
        $tunjangan = SalaryComponent::where('tipe', 'tunjangan')->orderBy('nama_komponen')->get();
        $potongan = SalaryComponent::where('tipe', 'potongan')->orderBy('nama_komponen')->get();

        return view('admin.komponen-gaji.index', compact('tunjangan', 'potongan'));
    }

    public function create(): View
    {
        return view('admin.komponen-gaji.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_komponen' => ['required', 'string', 'max:100'],
            'tipe' => ['required', 'in:tunjangan,potongan'],
            'jenis_nilai' => ['required', 'in:nominal,persentase'],
            'nilai' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        SalaryComponent::create($validated);

        return redirect()->route('admin.komponen-gaji.index')
            ->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    public function edit(SalaryComponent $salaryComponent): View
    {
        return view('admin.komponen-gaji.edit', compact('salaryComponent'));
    }

    public function update(Request $request, SalaryComponent $salaryComponent): RedirectResponse
    {
        $validated = $request->validate([
            'nama_komponen' => ['required', 'string', 'max:100'],
            'tipe' => ['required', 'in:tunjangan,potongan'],
            'jenis_nilai' => ['required', 'in:nominal,persentase'],
            'nilai' => ['required', 'numeric', 'min:0'],
            'is_aktif' => ['boolean'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $salaryComponent->update($validated);

        return redirect()->route('admin.komponen-gaji.index')
            ->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    public function destroy(SalaryComponent $salaryComponent): RedirectResponse
    {
        // Menyimpan nama komponen untuk ditampilkan di flash message
        $nama = $salaryComponent->nama_komponen;
        
        // Menghapus data dari database secara permanen
        $salaryComponent->delete();

        return redirect()->route('admin.komponen-gaji.index')
            ->with('success', 'Komponen gaji "' . $nama . '" berhasil dihapus secara permanen.');
    }
}
