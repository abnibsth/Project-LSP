---
name: anti-ai-slop
description: >
  Gunakan skill ini untuk mendeteksi, mencegah, dan memperbaiki "AI slop" — konten
  yang terasa generik, hambar, penuh klise, atau terlalu terstruktur seperti keluaran
  AI yang tidak dipikirkan. Trigger skill ini setiap kali pengguna meminta tulisan
  berkualitas tinggi, meminta revisi tulisan yang "terlalu AI", meminta teks yang
  terasa manusiawi/natural, atau menyebut kata-kata seperti: "jangan kayak AI",
  "terlalu formal", "terlalu template", "hambar", "slop", "terasa robot",
  "bikin yang lebih hidup", atau sejenisnya. Juga trigger saat Claude sendiri
  menyadari dirinya hampir menghasilkan konten berstruktur boilerplate tanpa
  kebutuhan nyata.
---

# AI Anti-Slop Skill

Skill ini membantu Claude menghasilkan tulisan yang **terasa nyata, punya suara,
dan tidak terasa dicetak mesin**. Ini bukan soal "lebih kreatif" secara abstrak —
ini soal menghindari pola spesifik yang menandakan teks AI generik.

---

## Apa Itu AI Slop?

AI slop adalah konten yang secara teknis benar tapi terasa kosong. Ciri-cirinya:

### Pola Bahasa yang Harus Dihindari
- **Frasa pembuka hampa**: "Tentu saja!", "Pertanyaan yang bagus!", "Berikut adalah...",
  "Dalam era modern ini...", "Di dunia yang terus berkembang..."
- **Bullet point berlebihan**: Mengubah setiap jawaban menjadi daftar padahal
  prosa lebih natural dan informatif
- **Struktur simetris palsu**: Setiap poin harus 2 kalimat, setiap bagian harus
  punya 3 sub-poin — terasa dipaksakan bukan organik
- **Kata-kata hedging berlebihan**: "Penting untuk diingat bahwa...", "Perlu
  diperhatikan...", "Tentunya hal ini bergantung pada..."
- **Penutup generik**: "Semoga bermanfaat!", "Jangan ragu untuk bertanya!",
  "Dengan demikian, kita dapat menyimpulkan bahwa..."
- **Bold yang tidak perlu**: **Menebalkan** kata-kata **acak** yang **sebenarnya**
  tidak perlu ditekankan
- **Kesimpulan yang merangkum ulang**: Paragraf penutup yang cuma mengulang apa
  yang baru saja dikatakan

### Pola Struktur yang Harus Dihindari
- Header untuk setiap paragraf kecil dalam percakapan biasa
- Intro → 3 poin → kesimpulan untuk semua jenis konten
- Menyebutkan berapa banyak poin yang akan dibahas sebelum membahasnya
- Menggunakan kata "Pertama", "Kedua", "Ketiga", "Terakhir" untuk hal-hal
  yang tidak perlu diurutkan

---

## Cara Kerja Skill Ini

### 1. Deteksi Mode
Saat menerima tulisan untuk diperiksa atau sebelum menghasilkan konten baru,
jalankan quick-check mental:

```
[ ] Ada frasa pembuka hampa?
[ ] Struktur bullet/header berlebihan untuk konten yang tidak memerlukannya?
[ ] Kata-kata hedging tidak perlu?
[ ] Penutup generik?
[ ] Kalimat yang panjang tapi tidak menambah informasi?
[ ] Terasa seperti template yang diisi, bukan pikiran yang ditulis?
```

Jika 2+ item tercentang → konten butuh perbaikan.

### 2. Revisi Mode
Saat memperbaiki konten yang mengandung slop:

**Langkah 1 — Potong dulu, tanya kemudian**
Hapus semua frasa hampa. Tidak ada yang hilang jika frasa itu dihapus.

**Langkah 2 — Ratakan struktur berlebihan**
Ubah bullet list yang tidak perlu menjadi prosa. Hapus header jika teks cukup
pendek untuk dibaca tanpa navigasi.

**Langkah 3 — Cari kalimat panjang tanpa isi**
Setiap kalimat harus *mengatakan sesuatu*. Jika kalimat bisa dihapus tanpa
kehilangan informasi → hapus.

**Langkah 4 — Tambahkan spesifisitas**
Konten slop biasanya terlalu abstrak. Ganti dengan contoh konkret, angka nyata,
atau detail yang hanya relevan untuk konteks spesifik ini.

**Langkah 5 — Periksa suara**
Bacakan dalam kepala. Apakah ada manusia yang berbicara seperti ini? Jika tidak
→ tulis ulang.

### 3. Produksi Mode
Saat menulis konten baru dari awal dengan skill ini aktif:

- **Mulai dari tengah**: Langsung ke inti tanpa intro ceremonial
- **Tulis prosa dulu**: Struktur hanya ditambahkan jika konten nyata-nyata
  memerlukannya (dokumentasi teknis, panduan langkah-langkah, perbandingan)
- **Satu ide, satu kalimat**: Hindari kalimat majemuk yang menggabungkan tiga
  ide dengan "dan", "serta", "juga"
- **Akhiri saat selesai**: Tidak ada penutup wajib. Jika sudah menjawab → berhenti.

---

## Contoh Transformasi

### ❌ Sebelum (AI Slop)
> Tentu saja! Berikut adalah beberapa tips penting untuk menulis email yang efektif:
>
> **1. Gunakan subjek yang jelas**
> Subjek email yang jelas sangat penting untuk memastikan penerima memahami isi email.
>
> **2. Tulis dengan singkat dan padat**
> Dalam era komunikasi modern, penting untuk menulis dengan singkat dan padat agar
> pesan tersampaikan dengan efektif.
>
> **3. Gunakan bahasa yang sopan**
> Bahasa yang sopan mencerminkan profesionalisme Anda.
>
> Semoga tips ini bermanfaat! Jangan ragu untuk bertanya jika ada pertanyaan lain.

### ✅ Sesudah (Anti-Slop)
> Subjek email yang buruk adalah alasan paling umum email diabaikan — tulis subjek
> seperti "Approval dibutuhkan sebelum Jumat" bukan "Follow up". Di dalam email,
> satu paragraf per topik. Jika butuh tiga paragraf panjang, mungkin ini harusnya
> meeting atau dokumen. Tutup dengan satu kalimat aksi yang jelas: siapa harus
> melakukan apa, kapan.

---

## Aturan Khusus per Konteks

### Percakapan / Chat
- Tidak perlu header sama sekali
- Prosa pendek, maks 3-4 kalimat per "blok pikiran"
- Boleh menggunakan bahasa sehari-hari jika sesuai register pengguna

### Email / Surat Profesional
- Satu paragraf pembuka yang langsung ke tujuan
- Isi: hanya informasi yang dibutuhkan penerima untuk bertindak
- Penutup: satu kalimat, bukan template "Atas perhatian Bapak/Ibu, kami ucapkan
  terima kasih"

### Artikel / Esai
- Pembuka dengan hook konkret — fakta mengejutkan, pertanyaan tajam, atau
  situasi spesifik — bukan definisi atau pernyataan luas
- Argumen mengalir, tidak harus tiga poin simetris
- Kesimpulan menambahkan sesuatu, tidak sekadar merangkum

### Konten Teknis / Dokumentasi
- Di sini struktur (header, bullet, numbered list) *memang diperlukan*
- Tapi tetap: setiap kalimat harus informatif, tidak ada filler
- Contoh kode/konkret lebih baik dari penjelasan abstrak

### Konten Kreatif
- Tidak ada formula
- Kekhasan lebih berharga dari kehalusan
- Satu detail yang tepat lebih kuat dari tiga paragraf deskripsi umum

---

## Frasa Penanda Slop (Referensi Cepat)

Jika menemukan salah satu frasa ini → langsung hapus atau tulis ulang:

**Pembuka:**
- "Tentu saja!", "Dengan senang hati!", "Pertanyaan yang bagus!"
- "Berikut adalah...", "Di bawah ini..."
- "Dalam era modern/digital/globalisasi ini..."
- "Tidak dapat dipungkiri bahwa..."

**Hedging:**
- "Penting untuk diingat bahwa..."
- "Perlu diperhatikan bahwa..."
- "Tentunya hal ini bergantung pada..."
- "Setiap situasi berbeda-beda..."

**Transisi Kosong:**
- "Selain itu, penting juga untuk..."
- "Tidak hanya itu, kita juga perlu..."
- "Lebih lanjut lagi..."

**Penutup:**
- "Semoga bermanfaat!"
- "Demikian penjelasan yang dapat saya sampaikan."
- "Jangan ragu untuk bertanya!"
- "Dengan demikian, dapat disimpulkan bahwa..."
- Paragraf yang hanya merangkum ulang apa yang baru dikatakan

---

## Catatan Penting

Skill ini **bukan** berarti semua tulisan harus pendek atau informal. Tulisan
panjang, formal, atau terstruktur bisa tetap anti-slop jika setiap bagiannya
punya alasan ada. Yang diperangi bukan panjang atau formalitas — tapi **konten
tanpa isi yang dibungkus struktur untuk terlihat lengkap**.

Jika ragu: potong dulu. Lebih baik singkat dan benar daripada panjang dan hampa.
