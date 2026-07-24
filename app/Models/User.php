<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User — merepresentasikan akun login di sistem.
 *
 * Ada dua role:
 * - 'admin'    : Admin/HRD — akses penuh ke semua fitur
 * - 'karyawan' : Karyawan biasa — hanya bisa akses data milik sendiri
 *
 * Setiap User (dengan role 'karyawan') TERHUBUNG ke satu Employee.
 */
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relasi: User ini punya satu data karyawan (Employee).
     * Dipakai untuk mengambil data karyawan dari objek user.
     *
     * Contoh: auth()->user()->employee->nama
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Karyawan::class);
    }

    /**
     * Helper: Cek apakah user ini adalah Admin/HRD.
     *
     * Penggunaan: if (auth()->user()->isAdmin()) { ... }
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper: Cek apakah user ini adalah Karyawan biasa.
     *
     * Penggunaan: if (auth()->user()->isKaryawan()) { ... }
     */
    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
