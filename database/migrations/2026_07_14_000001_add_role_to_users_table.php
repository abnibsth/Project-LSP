<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini menambahkan kolom 'role' ke tabel users.
 *
 * Role menentukan siapa yang bisa akses apa:
 * - 'admin' : Admin/HRD — bisa kelola semua data
 * - 'karyawan' : Karyawan biasa — hanya bisa lihat data sendiri
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom role: default 'karyawan' supaya aman
            $table->enum('role', ['admin', 'karyawan'])->default('karyawan')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
