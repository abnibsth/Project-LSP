<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\AttendanceRule;
use App\Models\Employee;
use App\Models\SalaryComponent;
use App\Models\User;
use Carbon\Carbon;
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
        $admin = User::updateOrCreate(
            ['email' => 'admin@ptnikel.com'],
            [
                'name' => 'Admin HRD',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

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
                    'gaji_pokok' => 6200000,
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
                    'gaji_pokok' => 6000000,
                    'no_rekening' => '1122334455',
                    'nama_bank' => 'Bank Mandiri',
                    'alamat' => 'Jl. Pertambangan No. 10, Sulawesi Tengah',
                    'no_telepon' => '083456789012',
                    'tanggal_masuk' => '2024-06-01',
                    'is_aktif' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'Dewi Lestari',
                    'email' => 'dewi@ptnikel.com',
                    'password' => Hash::make('password'),
                    'role' => 'karyawan',
                ],
                'employee' => [
                    'nik' => '3171010101960004',
                    'nama' => 'Dewi Lestari',
                    'jabatan' => 'Staff Keuangan',
                    'departemen' => 'Keuangan',
                    'status_kerja' => 'tetap',
                    'gaji_pokok' => 6500000,
                    'no_rekening' => '5566778899',
                    'nama_bank' => 'Bank BCA',
                    'alamat' => 'Jl. Industri No. 7, Sulawesi Tengah',
                    'no_telepon' => '084567890123',
                    'tanggal_masuk' => '2023-08-10',
                    'is_aktif' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'Rizky Pratama',
                    'email' => 'rizky@ptnikel.com',
                    'password' => Hash::make('password'),
                    'role' => 'karyawan',
                ],
                'employee' => [
                    'nik' => '3171010101980005',
                    'nama' => 'Rizky Pratama',
                    'jabatan' => 'Operator Alat Berat',
                    'departemen' => 'Operasional',
                    'status_kerja' => 'kontrak',
                    'gaji_pokok' => 5800000,
                    'no_rekening' => '6677889900',
                    'nama_bank' => 'Bank BRI (Bank Rakyat Indonesia)',
                    'alamat' => 'Jl. Tambang Raya No. 12, Sulawesi Tengah',
                    'no_telepon' => '085678901234',
                    'tanggal_masuk' => '2024-02-20',
                    'is_aktif' => true,
                ],
            ],
        ];

        foreach ($karyawanData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                $data['user']
            );

            Employee::updateOrCreate(
                ['nik' => $data['employee']['nik']],
                array_merge($data['employee'], ['user_id' => $user->id])
            );
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
            SalaryComponent::updateOrCreate(
                ['nama_komponen' => $komponen['nama_komponen']],
                $komponen
            );
        }

        // =============================================
        // 4. BUAT ATURAN ABSENSI DEFAULT
        // =============================================
        AttendanceRule::firstOrCreate(
            [
                'jam_masuk' => '07:30:00',
                'jam_keluar' => '17:00:00',
            ],
            [
                'toleransi_menit' => 15,            // Toleransi 15 menit keterlambatan
                'potongan_per_alpha' => 200000,      // Rp 200.000 per hari alpha
                'potongan_per_menit_telat' => 1000,  // Rp 1.000 per menit terlambat
            ]
        );

        // =============================================
        // 5. BUAT DATA ABSENSI BULAN JANUARI - JULI 2026
        // =============================================
        $bulanAbsensi = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
        ];
        $karyawanAktif = Employee::aktif()->get();

        foreach ($bulanAbsensi as $bulan => $namaBulan) {
            $tanggalMulai = Carbon::create(2026, $bulan, 1);
            $tanggalSelesai = $tanggalMulai->copy()->endOfMonth();

            foreach ($karyawanAktif as $indexKaryawan => $karyawan) {
                for ($tanggal = $tanggalMulai->copy(); $tanggal->lte($tanggalSelesai); $tanggal->addDay()) {
                    if ($tanggal->isWeekend()) {
                        continue;
                    }

                    $urutanHari = $tanggal->day + $bulan + $indexKaryawan;
                    $status = 'hadir';
                    $menitTerlambat = 0;
                    $waktuCheckin = $tanggal->copy()->setTime(7, 20 + ($urutanHari % 10));
                    $waktuCheckout = $tanggal->copy()->setTime(17, 0 + ($urutanHari % 20));
                    $keterangan = "Data absensi dummy {$namaBulan} 2026";

                    if ($urutanHari % 11 === 0) {
                        $status = 'alpha';
                        $waktuCheckin = null;
                        $waktuCheckout = null;
                        $keterangan = 'Tidak hadir tanpa keterangan';
                    } elseif ($urutanHari % 5 === 0) {
                        $status = 'telat';
                        $menitTerlambat = 10 + ($urutanHari % 35);
                        $waktuCheckin = $tanggal->copy()->setTime(7, 30)->addMinutes($menitTerlambat);
                        $waktuCheckout = $tanggal->copy()->setTime(17, 5);
                        $keterangan = 'Terlambat masuk kerja';
                    }

                    Absensi::updateOrCreate(
                        [
                            'employee_id' => $karyawan->id,
                            'tanggal' => $tanggal->toDateString(),
                        ],
                        [
                            'waktu_checkin' => $waktuCheckin,
                            'waktu_checkout' => $waktuCheckout,
                            'status' => $status,
                            'menit_terlambat' => $menitTerlambat,
                            'ip_address' => '127.0.0.1',
                            'is_koreksi' => true,
                            'keterangan' => $keterangan,
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ Data awal berhasil dibuat!');
        $this->command->info('👤 Admin: admin@ptnikel.com / password');
        $this->command->info('👤 Karyawan: budi@ptnikel.com / password');
        $this->command->info('👤 Karyawan: siti@ptnikel.com / password');
        $this->command->info('👤 Karyawan: ahmad@ptnikel.com / password');
        $this->command->info('👤 Karyawan: dewi@ptnikel.com / password');
        $this->command->info('👤 Karyawan: rizky@ptnikel.com / password');
    }
}
