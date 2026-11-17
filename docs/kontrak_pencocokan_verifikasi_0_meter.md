# Kontrak Pencocokan ID Pelanggan & Analisis Kelayakan Query Laporan Verifikasi 0 Meter

Dokumen ini menetapkan kontrak spesifikasi bisnis, analisis indeks/query lintas host, dan batasan data untuk implementasi rekonsiliasi terjadwal grafik verifikasi 0 meter, sesuai issue Beads `cater_web_ci4-b8k`.

---

## 1. Alur & Konteks Sistem Eksisting

### 1.1 Sumber Data
1. **Host Default (`pd` - MariaDB):**
   - Tabel `baca_meter` (MyISAM, ~16,6 juta baris).
   - Tabel referensi `munit` (19 baris unit kerja) dan `mcabang` (5 kantor cabang).
2. **Host GIS (`pdam_pwt` - MariaDB):**
   - Tabel `verif_0meter` (InnoDB, ~14.500 baris akumulatif).

### 1.2 Endpoint & Presentasi Eksisting
- **Endpoint:** `GET /api/laporan/grafik_verifikasi_0?tahun=YYYY` (`App\Controllers\Api\Laporan\GrafikVerifikasi0`).
- **Service Eksisting:** `App\Libraries\Verif0MeterGrafikService::getBulanan(int $tahun)`.
- **Tampilan:** `app/Views/laporan/grafik_verifikasi_0.php` & `public/assets/js/laporan/grafik_verifikasi_0.js` (Chart.js diagram batang bertumpuk per cabang 12 bulan).
- **Masalah Mendasar:** Service lama menjumlahkan baris agregat mentah `COUNT(*)` dari `verif_0meter` dan mengurangkannya langsung dari `COUNT(*)` `baca_meter`. Pendekatan ini tidak memverifikasi kesamaan nomor pelanggan (`no_sam` vs `nosamw`), sehingga aktivitas verifikasi di luar target keliru diakui sebagai realisasi target.

---

## 2. Kontrak Spesifikasi Bisnis & Pencocokan ID

### 2.1 Definisi Target Bulanan
- Target verifikasi bulan $M$ tahun $T$ berasal dari data pembacaan meter periode $M-1$ dengan kriteria:
  - `baca_meter.tgl >= :tgl_awal AND baca_meter.tgl < :tgl_akhir`
  - `LOWER(baca_meter.kondisi) = 'normal'`
  - `baca_meter.pakai = 0`
- **Pemetaan Periode:** Mengikuti formula `DATE_FORMAT(DATE_ADD(tgl, INTERVAL 1 MONTH), '%Y%m')`.
  - Khusus target Januari ($T$), periode baca sumber adalah Desember ($T-1$).
- **Unit Hitung:** Nomor pelanggan unik (`no_sam`). Jika satu pelanggan memiliki multiple baris baca meter pada bulan yang sama, dihitung tepat **1 kali** (`COUNT(DISTINCT no_sam)`).

### 2.2 Pemetaan Unit & Cabang
- Nomor pelanggan berformat 7 karakter (contoh `'0533742'`).
- Dua digit pertama (`SUBSTRING(no_sam, 1, 2)` pada `baca_meter` atau `LEFT(nosamw, 2)` pada `verif_0meter`) merupakan kode unit (`munit.unit`).
- Kode unit berelasi langsung dengan `munit.satker = mcabang.id_cabang` (5 cabang: Purwokerto 1 `0200`, Purwokerto 2 `0300`, Banyumas `0400`, Wangon `0500`, Ajibarang `0600`).
- Seluruh 19 kode unit pada kedua tabel terbukti valid dan memetakan secara konsisten ke 5 cabang.

### 2.3 Perlakuan Verifikasi Sebelum Bulan Target vs Bulan Berjalan
1. **Verifikasi Sebelum Awal Bulan Target ($tgl\_verif < \text{awal bulan } M$):**
   - Jika pelanggan yang masuk daftar target periode $M-1$ telah diverifikasi oleh petugas sebelum tanggal 1 bulan $M$ (misal dibaca tanggal 2 Desember, diverifikasi 16 Desember), verifikasi tersebut dicatat sebagai **Penyelesaian Dini / Pengurang Target Awal**.
   - Target Awal Bulan Bersih:
     $$\text{Target Awal Bulan } M = \text{Target Baca 0} - \text{Terverifikasi Sebelum Bulan } M$$
2. **Realisasi Verifikasi Bulan Berjalan ($tgl\_verif$ dalam bulan $M$):**
   - Hanya pelanggan target yang diverifikasi dalam rentang tanggal bulan $M$ yang dihitung sebagai **Realisasi Bulan Berjalan**.
   - Formula:
     $$\text{Realisasi Bulan } M = \text{Count ID Target yang Cocok dengan Verifikasi Bulan } M$$
3. **Verifikasi Non-Target / Aktivitas Mentah:**
   - Verifikasi pada bulan $M$ yang nomor pelanggannya TIDAK ada dalam target bulan $M$ **DILARANG** dihitung sebagai realisasi target bulan $M$.
   - Metrik ini dapat dilaporkan sebagai metrik terpisah: `aktivitas_verifikasi_lain`.
4. **Sisa Target Akhir Bulan:**
   $$\text{Sisa Target Akhir Bulan } M = \text{Target Awal Bulan } M - \text{Realisasi Bulan } M$$

### 2.4 Penanganan Duplikasi
- **Duplikasi Target:** Jika satu ID muncul lebih dari 1 kali di `baca_meter` pada bulan yang sama, set ID hanya menyimpan 1 entri unik.
- **Duplikasi Verifikasi:** Jika satu ID diverifikasi lebih dari 1 kali di `verif_0meter`, tanggal verifikasi pertama yang valid digunakan untuk menentukan periode realisasi. Verifikasi berulang pada pelanggan yang sama tidak boleh menambah hitungan realisasi ganda.

---

## 3. Analisis Status Verifikasi & Kebutuhan Keputusan Pemilik Produk

### 3.1 Temuan Struktur & Data Riil
Pemeriksaan pada tabel `pdam_pwt.verif_0meter` menunjukkan:
- **Kolom `status`:**
  - `0`: 12.173 baris
  - `NULL`: 2.165 baris
  - `2`: 139 baris
  - Tidak ada enum, comment skema, atau tabel referensi status di database GIS.
- **Kolom `hasil_verif`:**
  - `NULL`: 8.648 baris
  - Berisi teks bebas (free-text): 5.029 baris `'Normal'`, 139 baris `'Wm mati'`, sisanya ratusan variasi deskripsi operasional lapangan (misal `'rumah kosong'`, `'tutup sementara'`, `'tidak normal'`).
- **Kode Aplikasi Eksisting:**
  - Pada `Verif0MeterGrafikService` maupun `Verif0MeterModel`, tidak pernah ada filter kolom `status` atau `hasil_verif`. Semua baris dalam rentang tanggal `tgl_verif` dihitung.

### 3.2 Tuntutan Keputusan Pemilik Produk (Human Decision Gate)
> [!IMPORTANT]
> **Keputusan Produk yang Diperlukan:**
> 1. **Definisi Keabsahan Verifikasi:** Apakah semua baris verifikasi yang berhasil diinput petugas di lapangan (`verif_0meter`) atas ID pelanggan target diakui sebagai verifikasi yang sah (mengikuti logika sistem lama)? Atau hanya baris dengan `status` tertentu (misalnya `status != 2`)?
> 2. **Baseline Sementara (Tanpa Asumsi Liar):** Sesuai kriteria issue, rekonsiliasi tahap pertama menetapkan bahwa **kehadiran record verifikasi atas ID target diakui sebagai verifikasi terlaksana** (tanpa memfilter status yang belum memiliki aturan bisnis resmi). Namun, struktur generator CLI pada issue `cater_web_ci4-8dx` wajib mengekstrak atribut `status` agar filter dapat diterapkan segera setelah Pemilik Produk menetapkan aturan.

---

## 4. Evaluasi Indeks & Strategi Query Lintas Host

### 4.1 Pemeriksaan Indeks Eksisting

| Host / Database | Tabel | Jumlah Baris | Indeks Kunci | Kondisi Filter | Status Indeks |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Host Default (`pd`)** | `baca_meter` | ~16,6 Juta (MyISAM) | `KEY (tgl)`, `KEY (no_sam)`, `KEY (kondisi)` | `tgl`, `kondisi`, `pakai` | `pakai` tidak berindeks; `tgl` berindeks (Range Scan memindai ~95.000 baris per bulan, ~0,2 detik). |
| **Host GIS (`pdam_pwt`)** | `verif_0meter` | ~14.500 (InnoDB) | `KEY (tgl_verif)` | `tgl_verif` | `nosamw` **TIDAK** memiliki indeks; `tgl_verif` berindeks (< 0,05 detik untuk 1 tahun data). |

### 4.2 Evaluasi Pilihan Strategi Pembacaan

1. **Opsi A: Query Batch ID (`WHERE nosamw IN (...)`) ke Host GIS — DITOLAK**
   - *Alasan:* Karena `nosamw` tidak memiliki indeks di `verif_0meter`, mengeksekusi query `WHERE nosamw IN (1000 ID)` akan memicu Full Table Scan sebanyak puluhan kali. Ini sangat membebani server GIS.
2. **Opsi B: SQL Cross-Database JOIN — DITOLAK**
   - *Alasan:* Pada lingkungan produksi, kedua database berada pada host fisik MariaDB yang berbeda. MariaDB tidak mendukung query join cross-host secara native tanpa federated table (yang dilarang dan rapuh).
3. **Opsi C: Streaming Rentang Tanggal & Rekonsiliasi In-Memory Hash Set (DIREKOMENDASIKAN)**
   - *Alur Kerja:*
     1. Ambil seluruh verifikasi tahun target dari Host GIS menggunakan filter berindeks `tgl_verif >= :awal_tahun AND tgl_verif < :akhir_tahun` (hanya ~8.000 baris, memori < 2 MB, query < 0,05 detik). Simpan dalam struktur hash table PHP `$verifMap[nosamw][] = ['tgl' => ..., 'status' => ...]`.
     2. Ambil data target dari Host Default per bulan menggunakan filter berindeks `tgl` (11.000 baris per bulan, query ~0,2 detik).
     3. Lakukan pencocokan ID pelanggan di memori PHP (kompleksitas $O(1)$ lookup per target).
     4. Agregasi hasil per cabang dan per bulan, lalu simpan ke snapshot lokal.
   - *Estimasi Total Waktu:* < 3–5 detik untuk 12 bulan penuh.
   - *Penggunaan Memori:* < 20 MB RAM.

---

## 5. Batasan & Validasi Data

1. **Format String & Leading Zero:**
   - Kolom `no_sam` (`baca_meter`) dan `nosamw` (`verif_0meter`) harus selalu dibaca dan disimpan sebagai **string**.
   - Dilarang mengonversi nomor pelanggan ke tipe integer karena akan memotong angka nol di depan (misal `'0123456'` terpotong menjadi `123456`).
2. **Nilai Kosong / Anomali:**
   - Record dengan nomor pelanggan `NULL`, kosong `''`, atau bukan 7 digit angka dieksklusikan dari pencocokan dan dicatat dalam log anomali.
3. **Pergantian Tahun:**
   - Generator CLI untuk tahun $T$ wajib membaca pembacaan meter bulan Desember tahun $T-1$ untuk membentuk target bulan Januari tahun $T$.
   - Generator CLI juga wajib memuat verifikasi bulan Desember tahun $T-1$ untuk menghitung target yang telah terselesaikan sebelum 1 Januari tahun $T$.

---

## 6. Contoh Hitung Kecil (Perbandingan Metode Lama vs Baru)

Data riil diuji pada **Cabang Purwokerto 1 (`satker = '0200'`)**, periode **April 2026**:

| Parameter / Metrik | Metode Agregat Lama | Metode Pencocokan ID Baru | Selisih / Keterangan |
| :--- | :--- | :--- | :--- |
| **Sumber Target Baca 0 (Maret 2026)** | 3.409 baris | 3.409 ID unik | Sama (seluruh baris memiliki ID unik valid) |
| **Aktivitas Verifikasi Mentah (April 2026)** | 441 baris | 441 baris | Total seluruh verifikasi di cabang 0200 pada bulan April |
| **Realisasi Target yang Cocok (April 2026)** | **441** | **374** | **-67 pelanggan (-15,2%)** |
| **Verifikasi Non-Target / Di Luar Periode** | 0 (diabaikan) | 67 | 67 verifikasi bukan milik target Maret 2026 |
| **Target Sisa Akhir Bulan** | 2.664 (salah hitung akibat akumulasi) | 3.035 (3.409 - 374) | Perhitungan baru 100% akuntabel terhadap ID pelanggan |

> **Kesimpulan Bukti:** Metode lama melebih-lebihkan realisasi target sebesar **67 pelanggan (17,9% dari total realisasi valid)** karena mengklaim verifikasi non-target sebagai realisasi target bulan berjalan.

---

## 7. Status & Langkah Berikutnya

Kontrak ini menyelesaikan seluruh persyaratan investigasi dan spesifikasi untuk issue `cater_web_ci4-b8k`.
Setelah issue ini ditutup di Beads, pekerjaan dilanjutkan ke issue berikutnya:
- `cater_web_ci4-8dx`: Bangun proses CLI rekonsiliasi ID dan snapshot laporan lokal berdasarkan kontrak di atas.
