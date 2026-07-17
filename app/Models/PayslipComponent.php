<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model PayslipComponent — merepresentasikan satu baris rincian komponen di slip gaji.
 *
 * Contoh isi untuk slip gaji bulan Juli:
 *   payslip_id | nama_komponen       | tipe      | nilai
 *   -----------|---------------------|-----------|--------
 *   1          | Tunjangan Makan     | tunjangan | 500000
 *   1          | Tunjangan Transport | tunjangan | 300000
 *   1          | Kasbon              | potongan  | 1000000
 *
 * Data ini dipakai untuk menampilkan rincian di halaman slip gaji.
 */
#[Fillable([
    'payslip_id',
    'nama_komponen',
    'keterangan',
    'tipe',
    'nilai',
])]
class PayslipComponent extends Model
{
    use HasFactory;

    /**
     * Relasi: Komponen ini BAGIAN DARI satu slip gaji.
     */
    public function payslip(): BelongsTo
    {
        return $this->belongsTo(Payslip::class);
    }

    /**
     * Cast tipe data.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:2',
        ];
    }
}
