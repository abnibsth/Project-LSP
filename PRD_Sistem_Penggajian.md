# Product Requirements Document (PRD)
# Sistem Penggajian (Payroll System)

**Versi:** 1.3
**Tanggal:** 10 Juli 2026
**Status:** Draft
**Perubahan v1.3:** PPh 21 otomatis dihapus; diganti dengan Potongan Pajak yang diinput manual oleh HRD

---

## 1. Ringkasan Produk

Sistem Penggajian adalah aplikasi berbasis web yang digunakan untuk mengelola proses penggajian karyawan secara digital, mulai dari perhitungan gaji, potongan, tunjangan, hingga distribusi slip gaji. Sistem ini melibatkan dua jenis pengguna utama: **Admin/HRD** yang mengelola seluruh proses penggajian, dan **Karyawan** yang menerima dan melihat informasi gaji mereka.

## 2. Latar Belakang & Masalah

- Proses penggajian manual (Excel/manual input) rawan human error dan memakan waktu.
- Karyawan tidak memiliki akses mandiri untuk melihat riwayat gaji/slip gaji.
- Data absensi (hadir, telat, alpha) tidak terintegrasi dengan penggajian sehingga potongan kehadiran dihitung manual.

## 3. Tujuan Produk

1. Mengotomatisasi perhitungan gaji, tunjangan, dan potongan kehadiran.
2. Memberikan akses mandiri (self-service) bagi karyawan untuk melihat slip gaji.
3. Meningkatkan akurasi dan transparansi proses penggajian.
4. Mengintegrasikan data absensi (kehadiran, keterlambatan) langsung ke proses perhitungan gaji secara otomatis.

## 4. Target Pengguna & Role

| Role | Deskripsi |
|---|---|
| **Admin/HRD** | Bertanggung jawab mengelola data karyawan, komponen gaji, absensi, proses payroll bulanan, dan laporan. |
| **Karyawan** | Pengguna akhir yang dapat login untuk melakukan absensi harian, melihat slip gaji, dan riwayat penggajian. |

## 5. Ruang Lingkup (Scope)

### 5.1 In-Scope
- Manajemen data master karyawan (gaji pokok, jabatan, status kerja)
- Manajemen komponen gaji (tunjangan, potongan, bonus)
- Manajemen absensi karyawan (check-in/out, rekap kehadiran)
- Integrasi absensi ke payroll (potongan ketidakhadiran/keterlambatan otomatis ke perhitungan gaji)
- Proses hitung gaji bulanan (payroll run)
- Slip gaji digital (view & download PDF)
- Laporan penggajian (rekap bulanan)
- Laporan absensi (rekap kehadiran per karyawan/departemen)
- Riwayat gaji karyawan

### 5.2 Out of Scope
- Integrasi langsung ke sistem perbankan untuk transfer otomatis
- Integrasi perangkat fingerprint/face recognition (absensi diinput via web)
- Pengajuan izin / cuti / lembur oleh karyawan
- Approval workflow (izin, cuti, lembur, maupun payroll)
- Notifikasi email/push notification
- Perhitungan & laporan BPJS otomatis
- Audit trail perubahan data
- Aplikasi mobile native

## 6. User Roles & Hak Akses (Permission Matrix)

| Fitur | Admin/HRD | Karyawan |
|---|:---:|:---:|
| Kelola data karyawan | ✅ | ❌ |
| Kelola komponen gaji (tunjangan/potongan) | ✅ | ❌ |
| Input/koreksi absensi karyawan | ✅ | ❌ |
| Lihat rekap absensi semua karyawan | ✅ | ❌ |
| Lihat rekap absensi sendiri | ✅ | ✅ |
| Check-in / Check-out harian | ❌ | ✅ |
| Jalankan proses payroll | ✅ | ❌ |
| Lihat slip gaji semua karyawan | ✅ | ❌ |
| Lihat slip gaji sendiri | ✅ | ✅ |
| Download slip gaji (PDF) | ✅ | ✅ |
| Lihat riwayat gaji sendiri | ✅ | ✅ |
| Update data pribadi (rekening, alamat) | ✅ | ✅ |
| Lihat laporan & rekap payroll | ✅ | ❌ |
| Export laporan absensi / payroll | ✅ | ❌ |

## 7. Fitur Utama

### 7.1 Modul Admin/HRD

#### A. Manajemen Data Karyawan
- Tambah/edit/nonaktifkan data karyawan
- Input data gaji pokok, jabatan, departemen, status (tetap/kontrak/probation)
- Input data rekening bank untuk pembayaran

#### B. Manajemen Komponen Gaji
- Setup tunjangan (transport, makan, jabatan, dll)
- Setup potongan: **Potongan Pajak diinput manual oleh HRD per karyawan** (nominal langsung, tidak dihitung otomatis), pinjaman/kasbon, dan potongan lainnya
- Setup aturan bonus/THR

#### C. Manajemen Absensi
- Lihat rekap absensi harian/mingguan/bulanan seluruh karyawan
- Input/koreksi data absensi karyawan (misal lupa check-in)
- Setup aturan absensi: jam masuk/keluar, toleransi keterlambatan
- Setup aturan potongan otomatis: potongan alpha dan keterlambatan
- Export laporan absensi per karyawan/departemen ke Excel/PDF

#### D. Proses Payroll (Payroll Run)
- Pilih periode payroll (bulanan)
- Sistem menghitung otomatis: Gaji Bruto → Potongan (absensi + potongan pajak manual + potongan lain) → Gaji Bersih (take-home pay)
- Preview hasil perhitungan sebelum finalisasi
- Kunci (lock) data payroll yang sudah final agar tidak berubah

#### E. Slip Gaji & Distribusi
- Generate slip gaji otomatis dalam format PDF
- Riwayat slip gaji per karyawan per periode

#### F. Laporan
- Rekap payroll bulanan/tahunan
- Export ke Excel/PDF

### 7.2 Modul Karyawan

#### A. Dashboard Karyawan
- Ringkasan gaji terakhir (take-home pay)
- Status slip gaji bulan berjalan
- Ringkasan kehadiran bulan ini (hadir, telat, alpha)

#### B. Absensi Karyawan
- Check-in & Check-out harian via web (dengan timestamp otomatis)
- Lihat rekap kehadiran pribadi (status: Hadir / Telat / Alpha)

#### C. Slip Gaji
- Lihat detail slip gaji (gaji pokok, tunjangan, potongan kehadiran, potongan lain, gaji bersih)
- Download slip gaji dalam format PDF
- Riwayat slip gaji (per bulan/tahun)

#### D. Profil & Data Pribadi
- Lihat & update data pribadi (nomor rekening, alamat, kontak)

## 8. Alur Pengguna (User Flow)

### 8.1 Alur Karyawan — Absensi Harian
1. Login ke sistem
2. Buka menu "Absensi"
3. Klik tombol **Check-In** (sistem catat waktu otomatis)
4. Di akhir jam kerja, klik tombol **Check-Out**
5. Sistem menandai status: Hadir / Telat (jika melebihi toleransi) / Alpha (jika tidak check-in sama sekali)

### 8.2 Alur Admin/HRD — Proses Payroll Bulanan
1. Login ke sistem
2. Pastikan data absensi bulan berjalan sudah lengkap; koreksi jika perlu
3. Buka menu "Proses Payroll" → pilih periode payroll
4. Sistem menarik data karyawan aktif + komponen gaji (termasuk potongan pajak yang sudah diinput HRD) + rekap absensi (potongan keterlambatan/alpha dihitung otomatis)
5. Sistem menghitung gaji: Gaji Bruto → Potongan (absensi + potongan pajak manual + potongan lain) → Gaji Bersih
6. Admin/HRD review & koreksi jika perlu
7. Finalisasi → sistem generate slip gaji, karyawan dapat mengaksesnya langsung di sistem

### 8.3 Alur Karyawan — Melihat Slip Gaji
1. Login ke sistem
2. Buka menu "Slip Gaji"
3. Pilih periode yang ingin dilihat
4. Lihat detail rincian gaji (termasuk rincian potongan absensi)
5. Download PDF jika diperlukan

## 9. Kebutuhan Fungsional (Functional Requirements)

| ID | Deskripsi | Prioritas |
|---|---|---|
| FR-01 | Sistem harus dapat menyimpan data master karyawan | Must Have |
| FR-02 | Sistem harus dapat mengelola komponen gaji (tunjangan/potongan) yang fleksibel | Must Have |
| FR-03 | Sistem harus dapat menghitung gaji otomatis berdasarkan komponen yang ditetapkan | Must Have |
| FR-04 | Admin/HRD harus dapat menginput nominal Potongan Pajak secara manual per karyawan per periode | Must Have |
| FR-05 | Sistem harus dapat generate slip gaji dalam format PDF | Must Have |
| FR-06 | Karyawan harus bisa login dan melihat slip gaji miliknya sendiri saja | Must Have |
| FR-07 | Admin/HRD harus bisa melihat & mengelola slip gaji seluruh karyawan | Must Have |
| FR-08 | Karyawan harus dapat melakukan check-in dan check-out harian via web | Must Have |
| FR-09 | Sistem harus merekam timestamp check-in/out dan menentukan status kehadiran (Hadir/Telat/Alpha) | Must Have |
| FR-10 | Rekap absensi harus otomatis terhubung ke proses payroll (potongan kehadiran dihitung otomatis) | Must Have |
| FR-11 | Admin/HRD harus dapat melakukan koreksi data absensi karyawan | Must Have |
| FR-12 | Data payroll yang sudah final tidak dapat diedit tanpa proses "unlock" khusus | Should Have |
| FR-13 | Sistem harus dapat mengekspor laporan payroll dan absensi ke Excel/PDF | Should Have |

## 10. Kebutuhan Non-Fungsional (Non-Functional Requirements)

| Kategori | Kebutuhan |
|---|---|
| **Keamanan** | Data gaji terenkripsi; akses berbasis role (RBAC); autentikasi wajib (login + password) |
| **Privasi** | Karyawan hanya bisa melihat data gaji dan absensi miliknya sendiri |
| **Performa** | Proses payroll untuk hingga 500 karyawan selesai dalam < 2 menit |
| **Ketersediaan** | Uptime sistem minimal 99% |
| **Kepatuhan (Compliance)** | Sistem menyediakan kolom Potongan Pajak yang dapat diinput manual oleh HRD sesuai kebijakan perpajakan perusahaan |
| **Skalabilitas** | Mendukung penambahan jumlah karyawan tanpa penurunan performa signifikan |
| **Usability** | Antarmuka sederhana dan mobile-responsive |

## 11. Model Data (Gambaran Umum)

**Entitas utama:**
- `Employee` (id, nama, jabatan, departemen, status, no_rekening, gaji_pokok)
- `SalaryComponent` (id, nama_komponen, tipe [tunjangan/potongan], nominal/persentase)
- `AttendanceRule` (id, jam_masuk, jam_keluar, toleransi_menit, potongan_per_alpha, potongan_per_menit_telat)
- `Attendance` (id, employee_id, tanggal, waktu_checkin, waktu_checkout, status [hadir/telat/alpha], menit_terlambat)
- `PayrollPeriod` (id, bulan, tahun, status [draft/final])
- `Payslip` (id, employee_id, payroll_period_id, gaji_bruto, potongan_absensi, total_potongan, gaji_bersih, file_pdf)

## 12. Metrik Keberhasilan (Success Metrics)

- Pengurangan waktu proses payroll bulanan (misal dari 3 hari menjadi < 1 hari)
- 0 kesalahan perhitungan gaji setelah 3 bulan implementasi
- 90% karyawan mengakses slip gaji secara mandiri via sistem
- Potongan kehadiran pada slip gaji 100% akurat dan terotomasi (tidak ada koreksi manual)

## 13. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Potongan pajak diinput salah oleh HRD | Tampilkan ringkasan potongan sebelum finalisasi payroll agar HRD dapat review ulang |
| Data gaji bocor/diakses tidak sah | Enkripsi data, role-based access control |
| Resistensi karyawan/HRD terhadap sistem baru | Sediakan training & dokumentasi penggunaan |
| Karyawan lupa check-in/out | Sediakan fitur koreksi absensi oleh Admin/HRD |
| Manipulasi absensi (titip absen) | Catat IP address saat check-in/out |

## 14. Roadmap (Usulan Fase)

| Fase | Fitur |
|---|---|
| **Fase 1 (MVP)** | Login 2 role, manajemen data karyawan, komponen gaji, check-in/out absensi, proses payroll, slip gaji PDF, laporan dasar |
| **Fase 2** | Integrasi absensi → payroll otomatis, koreksi absensi oleh HRD, export laporan lengkap |
| **Fase 3** | Perhitungan BPJS otomatis, PPh 21 otomatis, approval workflow payroll |
| **Fase 4** | Pengajuan izin/cuti/lembur, notifikasi, integrasi fingerprint, aplikasi mobile |

---

*Dokumen ini adalah draft awal dan dapat disesuaikan berdasarkan diskusi lebih lanjut dengan stakeholder (HRD, Finance, IT).*
