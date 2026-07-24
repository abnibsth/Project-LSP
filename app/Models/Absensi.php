<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Attendance — merepresentasikan satu record absensi karyawan per hari.
 *
 * Bagaimana status ditentukan:
 * - 'hadir' : check-in ada, dan masuk dalam toleransi waktu
 * - 'telat' : check-in ada, tapi melebihi toleransi waktu
 * - 'alpha' : tidak ada check-in sama sekali
 *
 * Contoh:
 *   Jam masuk = 08:00, toleransi = 15 menit
 *   → Check-in 08:10 = HADIR
 *   → Check-in 08:30 = TELAT (30 menit terlambat, dipotong gajinya)
 *   → Tidak check-in = ALPHA (dipotong per hari alpha)
 */
#[Fillable([
    'employee_id',
    'tanggal',
    'waktu_checkin',
    'waktu_checkout',
    'status',
    'menit_terlambat',
    'ip_address',
    'is_koreksi',
    'keterangan',
])]
class Absensi extends Model
{
    use HasFactory;

    // Tentukan secara manual nama tabel di database karena default-nya (absensis) berbeda dengan nama tabel riil (absen)
    protected $table = 'absen';

    /**
     * Relasi: Record absensi ini MILIK satu karyawan.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'employee_id');
    }

    /**
     * Cast tipe data.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_checkin' => 'datetime',
            'waktu_checkout' => 'datetime',
            'menit_terlambat' => 'integer',
            'is_koreksi' => 'boolean',
        ];
    }

    /**
     * Scope: filter absensi berdasarkan bulan dan tahun.
     * Penggunaan: Absensi::bulan(7, 2026)->get()
     */
    public function scopeBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    /**
     * Helper: Hitung potongan absensi untuk record ini.
     * Mengambil aturan dari AttendanceRule untuk menghitung nilai potongnya.
     *
     * @param  AttendanceRule  $rule  Aturan absensi yang berlaku
     * @return float Total potongan dalam rupiah
     */
    public function hitungPotongan(Aturanabsen $rule): float
    {
        if ($this->status === 'alpha') {
            return (float) $rule->potongan_per_alpha;
        }

        if ($this->status === 'telat') {
            return $this->menit_terlambat * (float) $rule->potongan_per_menit_telat;
        }

        return 0.0;
    }
}
