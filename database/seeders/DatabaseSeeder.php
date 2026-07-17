<?php

namespace Database\Seeders;

use App\Models\AttendanceRule;
use App\Models\Employee;
use App\Models\SalaryComponent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder ini mengisi database dengan DATA AWAL untuk keperluan testing & demo.
 *
 * Data yang dibuat:
 * 1. Akun Admin/HRD (untuk login sebagai admin)
 * 2. Beberapa akun Karyawan beserta data employee-nya
 * 3. Komponen gaji default (tunjangan transport & makan)
 * 4. Aturan absensi default
 *
 * Cara jalankan: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. BUAT AKUN ADMIN/HRD
        // =============================================
        $admin = User::create([
            'name' => 'Admin HRD',
            'email' => 'admin@ptnikel.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // =============================================
        // 2. BUAT AKUN KARYAWAN + DATA EMPLOYEE
        // =============================================
        $karyawanData = [
            [
                'user' => [
                    'name' => 'Budi Santoso',
                    'email' => 'budi@ptnikel.com',
                    'password' => Hash::make('password'),
                    'role' => 'karyawan',
                ],
                'employee' => [
                    'nik' => '3171010101900001',
                    'nama' => 'Budi Santoso',
                    'jabatan' => 'Staff Operasional',
                    'departemen' => 'Operasional',
                    'status_kerja' => 'tetap',
                    'gaji_pokok' => 5000000,
                    'no_rekening' => '1234567890',
                    'nama_bank' => 'Bank BRI (Bank Rakyat Indonesia)',
                    'alamat' => 'Jl. Merdeka No. 1, Sulawesi Tengah',
                    'no_telepon' => '081234567890',
                    'tanggal_masuk' => '2023-01-01',
                    'is_aktif' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'Siti Rahayu',
                    'email' => 'siti@ptnikel.com',
                    'password' => Hash::make('password'),
                    'role' => 'karyawan',
                ],
                'employee' => [
                    'nik' => '3171010101920002',
                    'nama' => 'Siti Rahayu',
                    'jabatan' => 'Supervisor Lapangan',
                    'departemen' => 'Produksi',
                    'status_kerja' => 'tetap',
                    'gaji_pokok' => 7500000,
                    'no_rekening' => '0987654321',
                    'nama_bank' => 'Bank BNI (Bank Negara Indonesia)',
                    'alamat' => 'Jl. Nikel No. 5, Sulawesi Tengah',
                    'no_telepon' => '082345678901',
                    'tanggal_masuk' => '2022-03-15',
                    'is_aktif' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'Ahmad Fauzi',
                    'email' => 'ahmad@ptnikel.com',
                    'password' => Hash::make('password'),
                    'role' => 'karyawan',
                ],
                'employee' => [
                    'nik' => '3171010101950003',
                    'nama' => 'Ahmad Fauzi',
                    'jabatan' => 'Teknisi Mesin',
                    'departemen' => 'Teknik',
                    'status_kerja' => 'kontrak',
                    'gaji_pokok' => 5500000,
                    'no_rekening' => '1122334455',
                    'nama_bank' => 'Bank Mandiri',
                    'alamat' => 'Jl. Pertambangan No. 10, Sulawesi Tengah',
                    'no_telepon' => '083456789012',
                    'tanggal_masuk' => '2024-06-01',
                    'is_aktif' => true,
                ],
            ],
        ];

        // Buat user dan employee untuk setiap karyawan
        foreach ($karyawanData as $data) {
            $user = User::create($data['user']);
            Employee::create(array_merge($data['employee'], ['user_id' => $user->id]));
        }

        // =============================================
        // 3. BUAT KOMPONEN GAJI DEFAULT
        // =============================================
        $komponenGaji = [
            // Tunjangan — menambah gaji
            [
                'nama_komponen' => 'Tunjangan Transport',
                'tipe' => 'tunjangan',
                'jenis_nilai' => 'nominal',
                'nilai' => 300000,
                'is_aktif' => true,
                'keterangan' => 'Tunjangan biaya transportasi harian',
            ],
            [
                'nama_komponen' => 'Tunjangan Makan',
                'tipe' => 'tunjangan',
                'jenis_nilai' => 'nominal',
                'nilai' => 500000,
                'is_aktif' => true,
                'keterangan' => 'Tunjangan uang makan per bulan',
            ],
            // Potongan — mengurangi gaji
            [
                'nama_komponen' => 'Kasbon / Pinjaman',
                'tipe' => 'potongan',
                'jenis_nilai' => 'nominal',
                'nilai' => 0, // diinput manual per karyawan saat payroll
                'is_aktif' => true,
                'keterangan' => 'Potongan kasbon atau pinjaman karyawan',
            ],
        ];

        foreach ($komponenGaji as $komponen) {
            SalaryComponent::create($komponen);
        }

        // =============================================
        // 4. BUAT ATURAN ABSENSI DEFAULT
        // =============================================
        AttendanceRule::create([
            'jam_masuk' => '07:30:00',
            'jam_keluar' => '17:00:00',
            'toleransi_menit' => 15,            // Toleransi 15 menit keterlambatan
            'potongan_per_alpha' => 200000,      // Rp 200.000 per hari alpha
            'potongan_per_menit_telat' => 1000,  // Rp 1.000 per menit terlambat
        ]);

        $this->command->info('✅ Data awal berhasil dibuat!');
        $this->command->info('👤 Admin: admin@ptnikel.com / password');
        $this->command->info('👤 Karyawan: budi@ptnikel.com / password');
        $this->command->info('👤 Karyawan: siti@ptnikel.com / password');
        $this->command->info('👤 Karyawan: ahmad@ptnikel.com / password');
    }
}
