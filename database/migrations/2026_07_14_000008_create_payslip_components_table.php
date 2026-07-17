<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini membuat tabel 'payslip_components' (Rincian Komponen Per Slip Gaji).
 *
 * Tabel ini menyimpan DETAIL baris per baris komponen gaji di setiap slip.
 * Contoh isi untuk 1 slip gaji:
 *   - Tunjangan Makan     | tunjangan | Rp 500.000
 *   - Tunjangan Transport | tunjangan | Rp 300.000
 *   - Kasbon Juli         | potongan  | Rp 1.000.000
 *
 * Data ini ditampilkan di halaman slip gaji karyawan agar terlihat rinciannya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslip_components', function (Blueprint $table) {
            $table->id();

            // Milik slip gaji mana
            $table->foreignId('payslip_id')->constrained('payslips')->cascadeOnDelete();

            // Nama komponen (disimpan sebagai snapshot teks)
            $table->string('nama_komponen');

            // Keterangan komponen (disimpan sebagai snapshot teks, nullable)
            $table->string('keterangan')->nullable();

            // Tipe: tunjangan (nambah) atau potongan (ngurangi)
            $table->enum('tipe', ['tunjangan', 'potongan']);

            // Nilai dalam rupiah (sudah dikonversi, bukan persentase lagi)
            $table->decimal('nilai', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslip_components');
    }
};
