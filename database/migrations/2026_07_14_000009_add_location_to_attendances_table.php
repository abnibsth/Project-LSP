<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk menambahkan field lokasi (latitude & longitude)
     * saat karyawan melakukan check-in dan check-out.
     */
    public function up(): void
    {
        Schema::table('absen', function (Blueprint $table) {
            // Kolom lokasi untuk check-in
            $table->decimal('latitude_checkin', 10, 8)->nullable()->after('ip_address');
            $table->decimal('longitude_checkin', 11, 8)->nullable()->after('latitude_checkin');

            // Kolom lokasi untuk check-out
            $table->decimal('latitude_checkout', 10, 8)->nullable()->after('longitude_checkin');
            $table->decimal('longitude_checkout', 11, 8)->nullable()->after('latitude_checkout');
        });
    }

    /**
     * Kembalikan perubahan jika migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('absen', function (Blueprint $table) {
            $table->dropColumn([
                'latitude_checkin',
                'longitude_checkin',
                'latitude_checkout',
                'longitude_checkout',
            ]);
        });
    }
};
