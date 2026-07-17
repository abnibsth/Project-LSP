<?php

use App\Http\Controllers\Admin\Rekapabsen;
use App\Http\Controllers\Admin\AttendanceRuleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\Slipgaji;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SalaryComponentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Karyawan\AbsensiController as KaryawanAbsensiController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\PayslipController as KaryawanPayslipController;
use App\Http\Controllers\Karyawan\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes (Daftar URL Halaman) — Sistem Penggajian PT Nikel Indonesia
|--------------------------------------------------------------------------
|
| File ini mendefinisikan SEMUA halaman/URL yang ada di aplikasi ini.
| Setiap route punya:
| - URL (misal: /login, /admin/karyawan)
| - Controller yang menangani
| - Nama (untuk dipanggil dengan route('nama'))
| - Middleware (siapa yang boleh akses)
|
*/

// ============================================================
// HALAMAN PUBLIK (tidak perlu login)
// ============================================================

// Redirect halaman utama "/" ke halaman login
Route::get('/', fn () => redirect()->route('login'));

// Halaman Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Logout (POST untuk keamanan, bukan GET)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================================
// HALAMAN ADMIN/HRD (hanya role 'admin' yang bisa akses)
// ============================================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // --- Manajemen Karyawan ---
        // GET  /admin/karyawan         → daftar semua karyawan
        // GET  /admin/karyawan/tambah  → form tambah karyawan baru
        // POST /admin/karyawan         → simpan karyawan baru
        // GET  /admin/karyawan/{id}    → lihat detail karyawan
        // GET  /admin/karyawan/{id}/edit → form edit karyawan
        // PUT  /admin/karyawan/{id}    → simpan perubahan karyawan
        // POST /admin/karyawan/{id}/nonaktifkan → nonaktifkan karyawan
        Route::resource('karyawan', EmployeeController::class)->parameters(['karyawan' => 'employee']);
        Route::post('karyawan/{employee}/nonaktifkan', [EmployeeController::class, 'nonaktifkan'])->name('karyawan.nonaktifkan');

        // --- Komponen Gaji ---
        Route::resource('komponen-gaji', SalaryComponentController::class)->parameters(['komponen-gaji' => 'salaryComponent']);

        // --- Aturan Absensi ---
        Route::get('aturan-absensi', [AttendanceRuleController::class, 'edit'])->name('aturan-absensi.edit');
        Route::put('aturan-absensi', [AttendanceRuleController::class, 'update'])->name('aturan-absensi.update');

        // --- Absensi Karyawan (Admin view & koreksi) ---
        Route::get('absensi', [Rekapabsen::class, 'index'])->name('absensi.index');
        Route::get('absensi/{attendance}/koreksi', [Rekapabsen::class, 'editKoreksi'])->name('absensi.koreksi');
        Route::put('absensi/{attendance}/koreksi', [Rekapabsen::class, 'updateKoreksi'])->name('absensi.koreksi.update');

        // --- Proses Payroll ---
        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/buat', [PayrollController::class, 'create'])->name('payroll.create');
        Route::post('payroll', [PayrollController::class, 'store'])->name('payroll.store');
        Route::get('payroll/{payrollPeriod}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::post('payroll/{payrollPeriod}/proses', [PayrollController::class, 'proses'])->name('payroll.proses');
        Route::post('payroll/{payrollPeriod}/finalisasi', [PayrollController::class, 'finalisasi'])->name('payroll.finalisasi');

        // --- Slip Gaji (Admin) ---
        Route::get('slip-gaji', [Slipgaji::class, 'index'])->name('slip-gaji.index');
        Route::get('slip-gaji/{payslip}', [Slipgaji::class, 'show'])->name('slip-gaji.show');
        Route::get('slip-gaji/{payslip}/download', [Slipgaji::class, 'download'])->name('slip-gaji.download');
        Route::post('slip-gaji/{payslip}/potongan-pajak', [Slipgaji::class, 'updatePotonganPajak'])->name('slip-gaji.potongan-pajak');

        // --- Laporan ---
        Route::get('laporan/payroll', [ReportController::class, 'payroll'])->name('laporan.payroll');
        Route::get('laporan/absensi', [ReportController::class, 'absensi'])->name('laporan.absensi');
        Route::get('laporan/payroll/export', [ReportController::class, 'exportPayroll'])->name('laporan.payroll.export');
        Route::get('laporan/absensi/export', [ReportController::class, 'exportAbsensi'])->name('laporan.absensi.export');
    });

// ============================================================
// HALAMAN KARYAWAN (hanya role 'karyawan' yang bisa akses)
// ============================================================
Route::prefix('karyawan')
    ->name('karyawan.')
    ->middleware(['auth', 'role:karyawan'])
    ->group(function () {

        // Dashboard Karyawan
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');

        // --- Absensi Karyawan ---
        Route::get('absensi', [KaryawanAbsensiController::class, 'index'])->name('absensi.index');
        Route::post('absensi/checkin', [KaryawanAbsensiController::class, 'checkIn'])->name('absensi.checkin');
        Route::post('absensi/checkout', [KaryawanAbsensiController::class, 'checkOut'])->name('absensi.checkout');

        // --- Slip Gaji Karyawan ---
        Route::get('slip-gaji', [KaryawanPayslipController::class, 'index'])->name('slip-gaji.index');
        Route::get('slip-gaji/{payslip}', [KaryawanPayslipController::class, 'show'])->name('slip-gaji.show');
        Route::get('slip-gaji/{payslip}/download', [KaryawanPayslipController::class, 'download'])->name('slip-gaji.download');

        // --- Profil Karyawan ---
        Route::get('profil', [ProfileController::class, 'edit'])->name('profil.edit');
        Route::put('profil', [ProfileController::class, 'update'])->name('profil.update');
    });
