<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini membuat tabel 'absen' (Record Absensi Harian).
 *
 * Tabel ini mencatat absensi setiap karyawan setiap harinya:
 * - Kapan check-in dan check-out
 * - Status kehadiran: Hadir / Telat / Alpha
 * - Berapa menit terlambat (untuk hitung potongan)
 * - IP address saat check-in (untuk mencegah "titip absen")
 *
 * Satu karyawan = satu record per hari kerja.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absen', function (Blueprint $table) {
            $table->id();

            // Karyawan yang absen (FK ke tabel karyawan)
            $table->foreignId('employee_id')->constrained('karyawan')->cascadeOnDelete();

            // Tanggal absensi (misal: 2026-07-14)
            $table->date('tanggal');

            // Waktu check-in (nullable karena mungkin belum check-in / diinput manual)
            $table->timestamp('waktu_checkin')->nullable();

            // Waktu check-out (nullable karena mungkin belum check-out)
            $table->timestamp('waktu_checkout')->nullable();

            // Status kehadiran:
            // - 'hadir'  : masuk tepat waktu (dalam toleransi)
            // - 'telat'  : masuk melebihi toleransi
            // - 'alpha'  : tidak masuk sama sekali (tidak ada check-in)
            $table->enum('status', ['hadir', 'telat', 'alpha'])->default('alpha');

            // Berapa menit terlambat (0 jika hadir tepat waktu)
            $table->integer('menit_terlambat')->default(0);

            // IP address saat check-in (anti titip absen — dicatat dari request)
            $table->string('ip_address', 45)->nullable();

            // Apakah data ini dikoreksi oleh admin? (bukan dari check-in karyawan sendiri)
            $table->boolean('is_koreksi')->default(false);

            // Keterangan dari admin saat koreksi
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Satu karyawan hanya boleh punya SATU record per tanggal
            $table->unique(['employee_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absen');
    }
};
