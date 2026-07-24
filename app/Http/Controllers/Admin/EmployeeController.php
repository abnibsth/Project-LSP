<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * EmployeeController (Admin) — CRUD untuk data karyawan.
 *
 * Fitur:
 * - index()      : tampilkan daftar semua karyawan
 * - create()     : tampilkan form tambah karyawan baru
 * - store()      : simpan data karyawan baru ke database
 * - show()       : tampilkan detail satu karyawan
 * - edit()       : tampilkan form edit karyawan
 * - update()     : simpan perubahan data karyawan
 * - nonaktifkan(): nonaktifkan karyawan (soft delete — tidak dihapus)
 */
class EmployeeController extends Controller
{
    /**
     * Tampilkan daftar semua karyawan.
     * Bisa difilter berdasarkan departemen atau pencarian nama/NIK.
     */
    public function index(Request $request): View
    {
        $query = Karyawan::with('user');

        // Filter pencarian (nama atau NIK)
        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->cari.'%')
                    ->orWhere('nik', 'like', '%'.$request->cari.'%');
            });
        }

        // Filter departemen
        if ($request->filled('departemen')) {
            $query->where('departemen', $request->departemen);
        }

        // Filter status aktif
        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        $employees = $query->orderBy('nama')->paginate(15)->withQueryString();

        // Daftar departemen unik untuk dropdown filter
        $departemen = Karyawan::distinct()->pluck('departemen')->sort()->values();

        return view('admin.karyawan.index', compact('employees', 'departemen'));
    }

    /**
     * Tampilkan form untuk tambah karyawan baru.
     */
    public function create(): View
    {
        return view('admin.karyawan.create');
    }

    /**
     * Simpan data karyawan baru ke database.
     * Otomatis membuatkan akun user dengan role 'karyawan' dan password default 'password'.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'numeric', 'digits:16', 'regex:/^317/', 'unique:karyawan,nik'],
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'jabatan' => ['required', 'string', 'max:100'],
            'departemen' => ['required', 'string', 'max:100'],
            'status_kerja' => ['required', 'in:tetap,kontrak,probation'],
            'gaji_pokok' => ['required', 'numeric', 'min:0'],
            'no_rekening' => ['nullable', 'string', 'max:30'],
            'nama_bank' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'tanggal_masuk' => ['required', 'date'],
        ], [
            'nik.digits' => 'NIK harus berupa 16 digit angka KTP.',
            'nik.regex' => 'NIK harus diawali dengan angka 317 (DKI Jakarta).',
            'nik.unique' => 'NIK sudah terdaftar.',
            'email.unique' => 'Alamat email sudah digunakan.',
        ]);

        // Buat akun user untuk login karyawan
        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);

        // Buat data employee dan hubungkan ke user
        Karyawan::create([
            ...$validated,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan '.$validated['nama'].' berhasil ditambahkan. Password default: password');
    }

    /**
     * Tampilkan detail lengkap satu karyawan.
     */
    public function show(Karyawan $employee): View
    {
        $employee->load(['user', 'attendances' => fn ($q) => $q->latest()->limit(10), 'payslips.payrollPeriod']);

        return view('admin.karyawan.show', compact('employee'));
    }

    /**
     * Tampilkan form edit data karyawan.
     */
    public function edit(Karyawan $employee): View
    {
        return view('admin.karyawan.edit', compact('employee'));
    }

    /**
     * Simpan perubahan data karyawan.
     */
    public function update(Request $request, Karyawan $employee): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'numeric', 'digits:16', 'regex:/^317/', 'unique:karyawan,nik,'.$employee->id],
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'departemen' => ['required', 'string', 'max:100'],
            'status_kerja' => ['required', 'in:tetap,kontrak,probation'],
            'gaji_pokok' => ['required', 'numeric', 'min:0'],
            'no_rekening' => ['nullable', 'string', 'max:30'],
            'nama_bank' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'tanggal_masuk' => ['required', 'date'],
        ], [
            'nik.digits' => 'NIK harus berupa 16 digit angka KTP.',
            'nik.regex' => 'NIK harus diawali dengan angka 317 (DKI Jakarta).',
            'nik.unique' => 'NIK sudah terdaftar.',
        ]);

        $employee->update($validated);

        // Sinkronkan nama di tabel users juga
        if ($employee->user) {
            $employee->user->update(['name' => $validated['nama']]);
        }

        return redirect()->route('admin.karyawan.show', $employee)
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Nonaktifkan karyawan (bukan hapus permanen).
     * Karyawan yang nonaktif tidak akan diproses di payroll berikutnya.
     */
    public function nonaktifkan(Karyawan $employee): RedirectResponse
    {
        $employee->update(['is_aktif' => false]);

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan '.$employee->nama.' berhasil dinonaktifkan.');
    }

    /**
     * Hapus data karyawan (admin.karyawan.destroy via resource route).
     * Mengaktifkan kembali jika sebelumnya nonaktif, atau hapus jika diperlukan.
     */
    public function destroy(Karyawan $employee): RedirectResponse
    {
        $namaKaryawan = $employee->nama;

        // Aktifkan kembali jika sedang nonaktif
        if (! $employee->is_aktif) {
            $employee->update(['is_aktif' => true]);

            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Karyawan '.$namaKaryawan.' berhasil diaktifkan kembali.');
        }

        return redirect()->route('admin.karyawan.index')
            ->with('error', 'Gunakan tombol Nonaktifkan untuk menonaktifkan karyawan.');
    }
}
