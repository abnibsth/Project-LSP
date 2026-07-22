<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini membuat tabel 'priode_payroll' (Periode Penggajian).
 *
 * Setiap bulan, Admin/HRD membuat satu "periode payroll" sebelum menjalankan
 * proses hitung gaji. Tabel ini mencatat:
 * - Bulan & tahun penggajian (misal: Juli 2026)
 * - Status: 'draft' (masih bisa diedit) atau 'final' (sudah dikunci)
 *
 * Setelah di-finalisasi (status = final), data gaji tidak bisa diubah lagi.
 * Ini penting agar data slip gaji yang sudah dibagikan tidak berubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('priode_payroll', function (Blueprint $table) {
            $table->id();

            // Bulan (1-12)
            $table->tinyInteger('bulan')->unsigned();

            // Tahun (misal: 2026)
            $table->smallInteger('tahun')->unsigned();

            // Status periode:
            // - 'draft' : masih bisa diproses ulang / diedit
            // - 'final' : sudah dikunci, tidak bisa diubah
            $table->enum('status', ['draft', 'final'])->default('draft');

            // Kapan di-finalisasi (dikunci)
            $table->timestamp('tanggal_finalisasi')->nullable();

            // Admin mana yang melakukan finalisasi
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Setiap bulan+tahun hanya boleh ada satu periode
            $table->unique(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('priode_payroll');
    }
};
