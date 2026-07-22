<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini membuat tabel 'karyawan' (Data Master Karyawan).
 *
 * Tabel ini menyimpan semua informasi karyawan:
 * - Data diri (nama, jabatan, departemen)
 * - Data gaji pokok
 * - Data rekening bank untuk pembayaran gaji
 * - Status kerja (tetap/kontrak/probation)
 *
 * Setiap karyawan TERHUBUNG ke satu akun user (untuk login).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users (akun login karyawan)
            // nullable karena admin mungkin belum dibuatkan akun login
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Data identitas karyawan
            $table->string('nik')->unique(); // Nomor Induk Karyawan
            $table->string('nama');
            $table->string('jabatan');
            $table->string('departemen');

            // Status kerja: tetap, kontrak, atau probation
            $table->enum('status_kerja', ['tetap', 'kontrak', 'probation'])->default('probation');

            // Gaji pokok per bulan (dalam rupiah)
            $table->decimal('gaji_pokok', 15, 2)->default(0);

            // Data rekening bank untuk transfer gaji
            $table->string('no_rekening')->nullable();
            $table->string('nama_bank')->nullable();

            // Data pribadi
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->date('tanggal_masuk'); // Tanggal mulai kerja

            // Status aktif/nonaktif karyawan
            // Jika false = karyawan sudah keluar/resign, tidak diproses payroll
            $table->boolean('is_aktif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
