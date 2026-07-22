<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini membuat tabel 'gaji' (Slip Gaji).
 *
 * Setiap karyawan mendapatkan SATU slip gaji per periode payroll.
 * Tabel ini menyimpan HASIL AKHIR perhitungan gaji:
 *
 * RUMUS PERHITUNGAN GAJI:
 *   Gaji Bruto    = Gaji Pokok + Total Tunjangan
 *   Total Potongan = Potongan Absensi + Potongan Pajak (manual HRD) + Potongan Lain
 *   Gaji Bersih   = Gaji Bruto - Total Potongan
 *
 * Data disimpan sebagai "snapshot" — artinya meskipun gaji pokok karyawan
 * diubah nanti, data slip gaji lama tetap tidak berubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaji', function (Blueprint $table) {
            $table->id();

            // Karyawan pemilik slip gaji
            $table->foreignId('employee_id')->constrained('karyawan')->cascadeOnDelete();

            // Periode gaji (bulan/tahun)
            $table->foreignId('payroll_period_id')->constrained('priode_payroll')->cascadeOnDelete();

            // === SNAPSHOT DATA GAJI ===
            // Gaji pokok yang berlaku SAAT ITU (snapshot, bukan dari tabel karyawan langsung)
            $table->decimal('gaji_pokok', 15, 2)->default(0);

            // Total semua tunjangan (transport + makan + jabatan + dll)
            $table->decimal('total_tunjangan', 15, 2)->default(0);

            // Gaji Bruto = Gaji Pokok + Total Tunjangan
            $table->decimal('gaji_bruto', 15, 2)->default(0);

            // === POTONGAN ===
            // Potongan dari absensi (alpha + keterlambatan), dihitung OTOMATIS
            $table->decimal('potongan_absensi', 15, 2)->default(0);

            // Potongan Pajak — diinput MANUAL oleh HRD sesuai kebijakan perpajakan
            $table->decimal('potongan_pajak', 15, 2)->default(0);

            // Potongan lain-lain (kasbon, pinjaman, dll)
            $table->decimal('total_potongan_lain', 15, 2)->default(0);

            // Total semua potongan dijumlahkan
            $table->decimal('total_potongan', 15, 2)->default(0);

            // === GAJI BERSIH (Take-Home Pay) ===
            $table->decimal('gaji_bersih', 15, 2)->default(0);

            // Rincian lengkap komponen (disimpan sebagai JSON untuk histori)
            $table->json('detail_json')->nullable();

            // Path file PDF slip gaji yang sudah di-generate
            $table->string('file_pdf')->nullable();

            $table->timestamps();

            // Satu karyawan hanya boleh punya satu slip per periode
            $table->unique(['employee_id', 'payroll_period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaji');
    }
};
