<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Payslip — merepresentasikan slip gaji satu karyawan untuk satu periode.
 *
 * Ini adalah "hasil akhir" dari proses payroll.
 * Semua angka di sini sudah dihitung dan disimpan sebagai snapshot
 * agar tidak berubah meskipun data karyawan diedit di masa depan.
 *
 * RUMUS:
 *   gaji_bruto      = gaji_pokok + total_tunjangan
 *   total_potongan  = potongan_absensi + potongan_pajak + total_potongan_lain
 *   gaji_bersih     = gaji_bruto - total_potongan
 */
#[Fillable([
    'employee_id',
    'payroll_period_id',
    'gaji_pokok',
    'total_tunjangan',
    'gaji_bruto',
    'potongan_absensi',
    'potongan_pajak',
    'total_potongan_lain',
    'total_potongan',
    'gaji_bersih',
    'detail_json',
    'file_pdf',
])]
class Slipgaji extends Model
{
    use HasFactory;

    protected $table = 'gaji';

    /**
     * Relasi: Slip gaji ini MILIK satu karyawan.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'employee_id');
    }

    /**
     * Relasi: Slip gaji ini termasuk dalam satu periode payroll.
     */
    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(Periodepayrol::class, 'payroll_period_id');
    }

    /**
     * Relasi: Slip gaji punya banyak baris rincian komponen.
     */
    public function components(): HasMany
    {
        return $this->hasMany(Komponenslip::class, 'payslip_id');
    }

    /**
     * Cast tipe data.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gaji_pokok' => 'decimal:2',
            'total_tunjangan' => 'decimal:2',
            'gaji_bruto' => 'decimal:2',
            'potongan_absensi' => 'decimal:2',
            'potongan_pajak' => 'decimal:2',
            'total_potongan_lain' => 'decimal:2',
            'total_potongan' => 'decimal:2',
            'gaji_bersih' => 'decimal:2',
            'detail_json' => 'array',
        ];
    }

    /**
     * Helper: Format gaji bersih ke format rupiah.
     * Contoh: 4500000 → "Rp 4.500.000"
     */
    public function getGajiBersihFormatAttribute(): string
    {
        return 'Rp '.number_format((float) $this->gaji_bersih, 0, ',', '.');
    }
}
