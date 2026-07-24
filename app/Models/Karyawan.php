<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Employee — merepresentasikan data karyawan perusahaan.
 *
 * Setiap karyawan memiliki:
 * - Satu akun user (untuk login ke sistem)
 * - Banyak record absensi (satu per hari kerja)
 * - Banyak slip gaji (satu per bulan)
 */
#[Fillable([
    'user_id',
    'nik',
    'nama',
    'jabatan',
    'departemen',
    'status_kerja',
    'gaji_pokok',
    'no_rekening',
    'nama_bank',
    'alamat',
    'no_telepon',
    'tanggal_masuk',
    'is_aktif',
])]
class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    /**
     * Relasi: Karyawan PUNYA SATU akun user (untuk login).
     * belongsTo = "Employee ada di dalam User"
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Karyawan PUNYA BANYAK record absensi.
     * hasMany = "satu karyawan bisa punya banyak data absensi"
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Absensi::class, 'employee_id');
    }

    /**
     * Relasi: Karyawan PUNYA BANYAK slip gaji.
     */
    public function payslips(): HasMany
    {
        return $this->hasMany(Slipgaji::class, 'employee_id');
    }

    /**
     * Cast: otomatis konversi tipe data saat diambil dari database.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gaji_pokok' => 'decimal:2',
            'tanggal_masuk' => 'date',
            'is_aktif' => 'boolean',
        ];
    }

    /**
     * Scope: hanya ambil karyawan yang AKTIF.
     *
     * Penggunaan: Employee::aktif()->get()
     * Artinya: "ambil semua karyawan yang is_aktif = true"
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Helper: Format gaji pokok ke format rupiah yang mudah dibaca.
     * Contoh: 5000000 → "Rp 5.000.000"
     */
    public function getGajiPokokFormatAttribute(): string
    {
        return 'Rp '.number_format($this->gaji_pokok, 0, ',', '.');
    }
}
