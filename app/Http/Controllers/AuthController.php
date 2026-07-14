<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * AuthController — menangani proses Login dan Logout.
 *
 * Alurnya:
 * 1. User buka halaman login → showLoginForm()
 * 2. User isi email & password, klik "Masuk" → login()
 * 3. Sistem cek apakah login berhasil:
 *    - Berhasil + role 'admin'    → redirect ke /admin/dashboard
 *    - Berhasil + role 'karyawan' → redirect ke /karyawan/dashboard
 *    - Gagal                      → kembali ke halaman login dengan pesan error
 * 4. User klik "Keluar" → logout()
 */
class AuthController extends Controller
{
    /**
     * Tampilkan halaman form login.
     * Jika sudah login, langsung redirect ke dashboard sesuai role.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // Jika sudah login, jangan tampilkan halaman login lagi
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login');
    }

    /**
     * Proses form login yang dikirim oleh user.
     *
     * Validasi: email & password wajib diisi.
     * Jika login berhasil → redirect ke dashboard sesuai role.
     * Jika gagal → kembali ke form login dengan pesan error.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validasi input form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Coba login dengan email & password yang diinput
        // 'remember' = opsi "ingat saya" dari checkbox di form
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session untuk keamanan (mencegah session fixation attack)
            $request->session()->regenerate();

            return $this->redirectToDashboard();
        }

        // Login gagal — kembalikan ke form login dengan pesan error
        return back()
            ->withErrors(['email' => 'Email atau password tidak sesuai.'])
            ->onlyInput('email');
    }

    /**
     * Proses logout user.
     * Hapus session dan redirect ke halaman login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Hapus data session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil keluar dari sistem.');
    }

    /**
     * Helper: Arahkan user ke dashboard yang sesuai berdasarkan role-nya.
     *
     * Admin/HRD  → /admin/dashboard
     * Karyawan   → /karyawan/dashboard
     */
    private function redirectToDashboard(): RedirectResponse
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('karyawan.dashboard');
    }
}
