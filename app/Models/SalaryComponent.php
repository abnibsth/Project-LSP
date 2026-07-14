<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model SalaryComponent — merepresentasikan komponen gaji (tunjangan/potongan).
 *
 * Contoh data di tabel ini:
 * | nama_komponen       | tipe      | jenis_nilai | nilai   |
 * |---------------------|-----------|-------------|---------|
 * | Tunjangan Makan     | tunjangan | nominal     | 500000  |
 * | Tunjangan Transport | tunjangan | nominal     | 300000  |
 * | Tunjangan Jabatan   | tunjangan | persentase  | 10      | ← 10% dari gaji pokok
 * | Kasbon              | potongan  | nominal     | 0       | ← diinput per karyawan
 */
#[Fillable([
    'nama_komponen',
    'tipe',
    'jenis_nilai',
    'nilai',
    'is_aktif',
    'keterangan',
])]
class SalaryComponent extends Model
{
    use HasFactory;

    /**
     * Scope: hanya ambil komponen yang masih aktif.
     *
     * Penggunaan: SalaryComponent::aktif()->get()
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Scope: filter hanya tunjangan saja.
     * Penggunaan: SalaryComponent::aktif()->tunjangan()->get()
     */
    public function scopeTunjangan($query)
    {
        return $query->where('tipe', 'tunjangan');
    }

    /**
     * Scope: filter hanya potongan saja.
     */
    public function scopePotongan($query)
    {
        return $query->where('tipe', 'potongan');
    }

    /**
     * Helper: Hitung nilai NOMINAL dari komponen ini untuk karyawan tertentu.
     * Jika jenis_nilai = 'persentase', dihitung dari gaji pokok karyawan.
     * Jika jenis_nilai = 'nominal', langsung kembalikan nilainya.
     *
     * @param  float  $gajiPokok  Gaji pokok karyawan
     * @return float Nilai dalam rupiah
     */
    public function hitungNilai(float $gajiPokok): float
    {
        if ($this->jenis_nilai === 'persentase') {
            return ($this->nilai / 100) * $gajiPokok;
        }

        return (float) $this->nilai;
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
            'is_aktif' => 'boolean',
        ];
    }
}
