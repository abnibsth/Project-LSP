<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini membuat tabel 'attendance_rules' (Aturan Absensi).
 *
 * Tabel ini menyimpan SATU baris aturan jam kerja perusahaan:
 * - Jam masuk & pulang
 * - Toleransi keterlambatan (berapa menit boleh telat)
 * - Berapa potongan jika alpha (tidak masuk tanpa keterangan)
 * - Berapa potongan per menit keterlambatan
 *
 * Biasanya hanya ada 1 record di tabel ini (aturan berlaku untuk semua karyawan).
 * Admin bisa mengubahnya kapan saja.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->id();

            // Jam masuk resmi, misal: "08:00:00"
            $table->time('jam_masuk')->default('08:00:00');

            // Jam pulang resmi, misal: "17:00:00"
            $table->time('jam_keluar')->default('17:00:00');

            // Toleransi keterlambatan dalam menit
            // Misal: 15 = karyawan masih dianggap "Hadir" jika masuk sebelum 08:15
            $table->integer('toleransi_menit')->default(15);

            // Potongan per hari ALPHA (tidak masuk sama sekali), dalam rupiah
            // Misal: 200000 = Rp 200.000 dipotong jika alpha 1 hari
            $table->decimal('potongan_per_alpha', 15, 2)->default(0);

            // Potongan per MENIT keterlambatan, dalam rupiah
            // Misal: 1000 = Rp 1.000 dipotong per menit terlambat
            $table->decimal('potongan_per_menit_telat', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_rules');
    }
};
