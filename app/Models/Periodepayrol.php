<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model PayrollPeriod — merepresentasikan satu periode penggajian (per bulan).
 *
 * Alur kerja:
 * 1. Admin buat periode baru (status = 'draft')
 * 2. Admin jalankan proses hitung gaji (payroll run)
 * 3. Admin review hasilnya
 * 4. Admin finalisasi → status berubah jadi 'final', data dikunci
 *
 * Setelah 'final', slip gaji bisa dilihat karyawan.
 */
#[Fillable([
    'bulan',
    'tahun',
    'status',
    'tanggal_finalisasi',
    'finalized_by',
])]
class Periodepayrol extends Model
{
    use HasFactory;

    protected $table = 'priode_payroll';

    /**
     * Relasi: Periode ini punya banyak slip gaji (satu per karyawan).
     */
    public function payslips(): HasMany
    {
        return $this->hasMany(Slipgaji::class, 'payroll_period_id');
    }

    /**
     * Relasi: Admin yang melakukan finalisasi.
     */
    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    /**
     * Cast tipe data.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'tahun' => 'integer',
            'tanggal_finalisasi' => 'datetime',
        ];
    }

    /**
     * Helper: Cek apakah periode ini sudah final (dikunci).
     * Jika sudah final, data tidak boleh diedit lagi.
     */
    public function isFinal(): bool
    {
        return $this->status === 'final';
    }

    /**
     * Helper: Dapatkan nama bulan dalam Bahasa Indonesia.
     * Contoh: bulan=7 → "Juli"
     */
    public function getNamaBulanAttribute(): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $namaBulan[$this->bulan] ?? '-';
    }

    /**
     * Helper: Dapatkan label periode yang mudah dibaca.
     * Contoh: "Juli 2026"
     */
    public function getLabelAttribute(): string
    {
        return $this->nama_bulan.' '.$this->tahun;
    }
}
