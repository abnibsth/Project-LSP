<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini membuat tabel 'salary_components' (Komponen Gaji).
 *
 * Tabel ini menyimpan daftar semua komponen yang mempengaruhi gaji:
 * - TUNJANGAN: yang MENAMBAH gaji (misal: tunjangan makan, transport, jabatan)
 * - POTONGAN: yang MENGURANGI gaji (misal: kasbon, potongan lain-lain)
 *
 * CATATAN: Potongan Pajak (PPh 21) diinput MANUAL oleh HRD saat proses payroll,
 * bukan dari tabel ini. Lihat kolom 'potongan_pajak' di tabel payslips.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();

            // Nama komponen, misal: "Tunjangan Makan", "Tunjangan Transport"
            $table->string('nama_komponen');

            // Apakah ini tunjangan (nambah gaji) atau potongan (ngurangi gaji)?
            $table->enum('tipe', ['tunjangan', 'potongan']);

            // Jenis nilai:
            // - 'nominal'    : angka tetap, misal Rp 500.000
            // - 'persentase' : persen dari gaji pokok, misal 10%
            $table->enum('jenis_nilai', ['nominal', 'persentase'])->default('nominal');

            // Nilai nominalnya (dalam rupiah atau persen)
            $table->decimal('nilai', 15, 2)->default(0);

            // Apakah komponen ini masih aktif dipakai?
            $table->boolean('is_aktif')->default(true);

            // Keterangan tambahan (opsional)
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
