# Sistem Penggajian (Payroll) PT Nikel Indonesia

Sistem Penggajian PT Nikel Indonesia adalah sebuah aplikasi berbasis web (Project LSP) yang dirancang untuk mendigitalisasi dan mengotomatisasi seluruh proses manajemen karyawan, pencatatan kehadiran, hingga perhitungan dan distribusi slip gaji bulanan (payroll) secara aman, akurat, dan transparan.

Aplikasi ini memiliki 2 hak akses (Role): **Admin HRD** dan **Karyawan**.

---

## 🚀 Fitur Utama

### 1. Panel Admin HRD
*   **Dashboard Utama**: Menampilkan ringkasan data real-time (Total Karyawan Aktif, Jumlah Karyawan Hadir & Alpha Hari Ini, Periode Payroll Aktif) beserta **Grafik Pengeluaran Gaji Bulanan** yang interaktif menggunakan Chart.js.
*   **Manajemen Karyawan**: Mengelola data profil karyawan. Input NIK divalidasi ketat menggunakan format KTP DKI Jakarta (16 digit angka diawali kode `317`).
*   **Komponen Gaji**: Mendefinisikan tunjangan (penambah gaji) dan potongan (pengurang gaji) yang berlaku secara dinamis, lengkap dengan kolom **Keterangan** penjelasan komponen.
*   **Aturan Absensi**: Pengaturan parameter absensi kantor (Jam Masuk, Jam Keluar, Toleransi Keterlambatan dalam menit, Potongan per hari Alpha, dan Potongan per menit terlambat).
*   **Rekap Absensi (Date Range Filter)**: Melihat logs kehadiran seluruh karyawan dengan filter **Rentang Tanggal** (Tanggal Mulai s/d Tanggal Selesai), filter Nama Karyawan, dan filter Status Kehadiran. Admin juga dapat melakukan koreksi/koreksi absensi jika karyawan lupa melakukan check-in/out.
*   **Proses Payroll Bulanan**: Memproses perhitungan gaji bulanan seluruh karyawan secara massal berdasarkan data kehadiran kerja nyata dalam periode tersebut.
*   **Manajemen Slip Gaji**: Melihat rincian komponen gaji bersih per karyawan dan mencetaknya ke format PDF resmi.
*   **Laporan Lanjutan**: Menyediakan menu khusus Laporan Payroll dan Laporan Absensi yang dapat diekspor.

### 2. Panel Karyawan
*   **Dashboard Karyawan**: Ringkasan data absensi pribadi bulan berjalan.
*   **Absensi Elektronik**: Melakukan Check-In di pagi hari dan Check-Out di sore hari secara mandiri. Sistem otomatis mendeteksi status kehadiran (*Hadir* atau *Terlambat* berdasarkan aturan kantor) beserta pencatatan IP Address.
*   **Riwayat Kehadiran**: Rekapitulasi kehadiran pribadi karyawan per tanggal.
*   **Slip Gaji Saya**: Melihat rincian slip gaji bulanan dan mendownload slip resmi format PDF jika periode payroll sudah difinalisasi oleh Admin.
*   **Profil Saya**: Memperbarui informasi rekening bank pribadi (Nama Bank, Nomor Rekening, Alamat, No Telepon) secara mandiri.

---

## 🛠️ Tech Stack & Dependencies

*   **Framework**: Laravel v10 / v11 (PHP 8.3)
*   **Database**: MySQL / MariaDB
*   **Styling**: Tailwind CSS & CSS Grid Layout
*   **Engine PDF**: Barryvdh/Laravel-Dompdf
*   **Visualisasi Data**: Chart.js (Interactive Line Chart dengan Gradient Fill)

---

## 🔄 Alur & Flow Sistem (Sistem Penggajian)

Berikut adalah diagram alir proses operasional sistem dari input data hingga terbitnya slip gaji:

```
[Mulai]
   │
   ▼
[1. Admin HRD Login]
   │
   ├─► [Atur Aturan Absensi] (Jam kerja & tarif denda terlambat/alpha)
   ├─► [Atur Komponen Gaji] (Tunjangan Makan, Transport, BPJS, PPh21, dll)
   └─► [Input Data Karyawan] (NIK divalidasi 16-digit berawalan 317)
   │
   ▼
[2. Operasional Karyawan (Setiap Hari)]
   │
   ├─► Karyawan Login -> Masuk ke Menu Absensi
   └─► Klik [Check-In] (Pagi) & [Check-Out] (Sore)
         ├── Status 'Hadir': Jika check-in <= jam masuk + toleransi menit
         └── Status 'Telat': Jika check-in > batas toleransi (dihitung menit terlambatnya)
   │
   ▼
[3. Akhir Bulan / Periode Payroll]
   │
   ├─► Admin masuk ke menu [Proses Payroll] -> Klik [Buat Periode Payroll] (Status: Draft)
   ├─► Klik [Proses Payroll]
   │     │
   │     ▼ (Sistem Menghitung Otomatis per Karyawan)
   │     • Gaji Pokok (diambil dari profil Karyawan)
   │     • Tunjangan Aktif (Transport, Makan, dll)
   │     • Potongan Kehadiran:
   │         - Hari Alpha × Potongan per Alpha
   │         - Menit Telat × Potongan per Menit Telat
   │     • Potongan Pajak (PPh 21) & Potongan Lainnya
   │     • Gaji Bersih (THP) = Gaji Pokok + Tunjangan - Potongan - Pajak
   │
   ├─► Admin memeriksa rincian slip gaji seluruh karyawan (Status: Draft)
   └─► Admin Klik [Finalisasi Periode] (Status berubah menjadi: Final)
   │
   ▼
[4. Distribusi Slip Gaji]
   │
   ├─► Karyawan Login -> Masuk ke Menu [Slip Gaji]
   └─► Klik [Download PDF]
         └── Terbit PDF Slip Gaji Resmi satu halaman A4 yang berisi:
               - Sisi Kiri: Log kehadiran real detail per tanggal (IN, OUT, Status, Sum Jam Kerja)
               - Sisi Kanan: Basis Gaji, Upah Bruto, Potongan Pajak & Absen, serta Gaji Bersih (THP)
   │
   ▼
[Selesai]
```

---

## 💻 Panduan Instalasi & Pengujian Lokal

1.  **Clone / Download Project**:
    Pastikan folder project diletakkan di direktori web server (misal: `laragon/www/PT-Nikel-Indonesia`).

2.  **Konfigurasi Environment (`.env`)**:
    Buat file `.env` dan sesuaikan koneksi database MySQL Anda:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=pt_nikel_payroll
    DB_USERNAME=root
    DB_PASSWORD=
    ```

3.  **Install Dependencies & Migrasi Database**:
    Jalankan perintah berikut di terminal:
    ```bash
    composer install
    npm install
    npm run build
    php artisan key:generate
    php artisan migrate:fresh --seed
    ```

4.  **Akun Login Default (Seeder)**:
    Gunakan akun-akun berikut untuk menguji sistem:
    *   **Admin HRD**:
        *   Email: `admin@ptnikel.com`
        *   Password: `password`
    *   **Karyawan (Budi Santoso)**:
        *   Email: `budi@ptnikel.com`
        *   Password: `password`
    *   **Karyawan (Siti Rahayu)**:
        *   Email: `siti@ptnikel.com`
        *   Password: `password`

---

## 📈 Database Schema Relations (Core)

*   `users`: Menyimpan kredensial akun login (`email`, `password`, `role`).
*   `employees`: Menyimpan data induk kepegawaian (`nik`, `nama`, `jabatan`, `gaji_pokok`, `rekening`, `is_aktif`). Berelasi One-to-One dengan `users`.
*   `attendances` (Model: `Absensi`): Pencatatan logs kehadiran harian (`tanggal`, `waktu_checkin`, `waktu_checkout`, `status`, `menit_terlambat`). Berelasi Many-to-One dengan `employees`.
*   `payroll_periods`: Menyimpan master data bulan & tahun periode penggajian (`bulan`, `tahun`, `status`).
*   `payslips`: Ringkasan gaji bersih per karyawan pada periode tertentu (`gaji_pokok`, `gaji_bruto`, `total_potongan`, `potongan_absensi`, `potongan_pajak`, `gaji_bersih`, `detail_json`). Berelasi Many-to-One dengan `payroll_periods` & `employees`.
*   `payslip_components`: Menyimpan snapshot rincian tunjangan/potongan yang diterima karyawan pada slip gaji (`nama_komponen`, `tipe`, `nilai`, `keterangan`). Berelasi Many-to-One dengan `payslips`.
