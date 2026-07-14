<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EnsureRole — memastikan user punya role yang dibutuhkan.
 *
 * Middleware ini dipasang di route untuk membatasi akses.
 * Contoh:
 *   Route::middleware('role:admin') → hanya admin yang bisa akses
 *   Route::middleware('role:karyawan') → hanya karyawan yang bisa akses
 *
 * Jika user tidak punya role yang sesuai → redirect ke dashboard mereka sendiri.
 * Jika belum login sama sekali → redirect ke halaman login.
 */
class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     * @param  string  $role  Role yang dibutuhkan ('admin' atau 'karyawan')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Cek apakah role user sesuai dengan yang dibutuhkan
        if ($request->user()->role !== $role) {
            // User salah role → arahkan ke dashboard mereka sendiri
            if ($request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        // Role sesuai → lanjutkan request ke halaman yang dituju
        return $next($request);
    }
}
