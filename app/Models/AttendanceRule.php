<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model AttendanceRule — menyimpan aturan absensi perusahaan.
 *
 * Biasanya hanya ada SATU baris di tabel ini.
 * Admin bisa ubah kapan saja melalui halaman pengaturan absensi.
 *
 * Cara pakai:
 *   $rule = AttendanceRule::first(); // Ambil aturan yang berlaku
 *   $rule->jam_masuk;                // "08:00:00"
 *   $rule->toleransi_menit;          // 15
 */
#[Fillable([
    'jam_masuk',
    'jam_keluar',
    'toleransi_menit',
    'potongan_per_alpha',
    'potongan_per_menit_telat',
])]
class AttendanceRule extends Model
{
    use HasFactory;

    protected $table = 'rules_absen';

    /**
     * Cast tipe data.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'toleransi_menit' => 'integer',
            'potongan_per_alpha' => 'decimal:2',
            'potongan_per_menit_telat' => 'decimal:2',
        ];
    }

    /**
     * Helper: Ambil aturan yang berlaku (record pertama).
     * Jika belum ada, buat dengan nilai default.
     */
    public static function berlaku(): static
    {
        return static::firstOrCreate([], [
            'jam_masuk' => '08:00:00',
            'jam_keluar' => '17:00:00',
            'toleransi_menit' => 15,
            'potongan_per_alpha' => 0,
            'potongan_per_menit_telat' => 0,
        ]);
    }
}
