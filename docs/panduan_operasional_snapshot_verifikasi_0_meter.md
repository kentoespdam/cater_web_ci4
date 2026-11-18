# Panduan Operasional & Jadwal Pembaruan Snapshot Verifikasi 0 Meter

Dokumen ini disusun sebagai panduan teknis operasional bagi tim DevOps, System Administrator, dan Operator aplikasi PDAM dalam mengelola, memantau, dan memelihara proses generator snapshot verifikasi 0 meter, sesuai issue Beads `cater_web_ci4-0cc`.

---

## 1. Arsitektur & Prinsip Kerja Snapshot

1. **Pemisahan Beban (Decoupling):**
   - Halaman laporan web (`GET /laporan/grafik_verifikasi_0`) dan endpoint API (`GET /api/laporan/grafik_verifikasi_0`) **hanya membaca file snapshot JSON lokal** di server web.
   - Tidak ada inisialisasi koneksi database MariaDB Host Default (`pd`) maupun Host GIS (`pdam_pwt`) pada saat request web berlangsung. Response time web berkurang dari beberapa detik menjadi < 1 milidetik.
2. **Penerbitan Atomik (Atomic Publishing):**
   - Generator CLI menulis hasil rekonsiliasi ke file sementara (`.tmp.<PID>`).
   - File sementara hanya dipindahkan (`rename`) ke nama file snapshot resmi setelah seluruh proses pembacaan dan kalkulasi berhasil 100%.
   - Jika terjadi kegagalan (koneksi database putus, timeout, dsb.), file sementara otomatis dibersihkan dan snapshot terakhir yang valid tetap utuh melayani grafik web.
3. **Pencegahan Proses Ganda (Locking):**
   - Menggunakan mekanisme OS file lock non-blocking (`flock(LOCK_EX | LOCK_NB)`).
   - Jika ada proses rekonsiliasi yang sedang berjalan untuk tahun yang sama, proses kedua akan keluar dengan pesan peringatan tanpa mengganggu proses pertama.

---

## 2. Lokasi Penyimpanan, Izin & Ruang Disk

### 2.1 Lokasi File
- **Direktori Snapshot:** `writable/snapshots/`
- **File Snapshot:** `writable/snapshots/verif_0meter_{tahun}.json` (contoh: `verif_0meter_2026.json`)
- **File Lock:** `writable/snapshots/.verif_0meter_{tahun}.lock`

### 2.2 Izin File & Kepemilikan (Permissions)
Direktori `writable/snapshots/` harus dapat dibaca dan ditulis oleh user yang menjalankan web server (misal `www-data`, `nginx`, atau `dev`) dan user cron job:
```bash
# Pastikan direktori tersedia
mkdir -p writable/snapshots

# Set kepemilikan ke user web/app
chown -R www-data:www-data writable/snapshots

# Set izin direktori dan file
chmod 755 writable/snapshots
chmod -R 644 writable/snapshots/*.json 2>/dev/null || true
```

### 2.3 Kebutuhan Ruang Disk
- Ukuran 1 file snapshot JSON untuk 1 tahun (5 cabang, 12 bulan, metrik agregat) berkisar antara **3 KB hingga 5 KB**.
- Menyimpan snapshot selama 10 tahun hanya membutuhkan kapasitas disk **< 100 KB**.
- Tidak memerlukan rotasi file agresif, namun disarankan mempertahankan snapshot untuk tahun aktif dan 1 tahun sebelumnya.

---

## 3. Rekomendasi Jadwal Pembaruan (Cron)

### 3.1 Analisis Beban & Kesegaran Data
- **Durasi Eksekusi Generator:** Berdasarkan pengujian pada database produksi/staging riil (~16,6 juta baris baca meter dan ~14.500 baris verifikasi), proses pembuatan snapshot 1 tahun penuh memakan waktu **~8–10 detik**.
- **Karakteristik Sumber Data:**
  - Data `baca_meter` diunggah berkala oleh petugas pembaca meter.
  - Data `verif_0meter` diinput oleh petugas verifikasi lapangan selama jam kerja operasional (08:00 – 16:00).
- **Rekomendasi Jadwal:**
  - **Jadwal Standar Produksi (Konsolidasi Harian):** Setiap malam hari pukul **01:00 WIB**.
  - **Jadwal Intensif (Pembaruan Berkala Jam Kerja):** Setiap **3 jam** sekali antara pukul 08:00 hingga 17:00 (misal pukul 09:00, 12:00, 15:00, dan 18:00).

### 3.2 Pemasangan Jadwal Cron
Pada deployment Docker Compose, service `cron` menjalankan generator setiap hari pukul **01:00 WIB**, sesuai jadwal standar produksi. Service memakai image PHP aplikasi, direktori kode dan volume `writable_data` yang sama, serta mount `.env` read-only. Baris jadwal berada di `docker/php/crontab`; proses Spark dijalankan sebagai `www-data` dan log ditulis ke `writable/logs/cron_verif0_snapshot.log`.

Aktifkan service setelah rebuild image dengan Compose modern:
```bash
docker compose --env-file .env.docker up -d --build cron
docker compose --env-file .env.docker logs cron
```

Untuk konfigurasi kompatibilitas `docker-compose-old.yml`, target `make up-old` juga menjalankan service `cron`. Atau jalankan langsung:
```bash
make build-old
make up-old
```

Pada host non-Docker, pemasangan cron dilakukan dengan memanggil Spark CLI tanpa menyertakan kredensial:
```bash
# Buka crontab user aplikasi (misal: crontab -u www-data -e)

# Opsi 1: Pembaruan harian pukul 01:00 WIB (jadwal host non-Docker)
0 1 * * * cd /var/www/html && php spark laporan:generate-verif0-snapshot >> writable/logs/cron_verif0_snapshot.log 2>&1

# Opsi 2: Pembaruan berkala setiap 3 jam pada hari kerja
0 9,12,15,18 * * 1-5 cd /var/www/html && php spark laporan:generate-verif0-snapshot >> writable/logs/cron_verif0_snapshot.log 2>&1
```

> [!CAUTION]
> Dilarang menaruh password database atau API key pada baris crontab. Kredensial database telah dikelola aman melalui file `.env`.

---

## 4. Pemantauan Kesegaran & Penanganan Error

### 4.1 Memeriksa Kesegaran Snapshot
Operator dapat memeriksa metadata pembaruan terakhir langsung dari file snapshot menggunakan perintah terminal:
```bash
# Menampilkan waktu pembaruan dan status snapshot
cat writable/snapshots/verif_0meter_2026.json | grep -E '"(updated_at|source_status)"'

# Atau menggunakan jq
jq '{tahun, updated_at, source_status}' writable/snapshots/verif_0meter_2026.json
```
Contoh output normal:
```json
{
  "tahun": 2026,
  "updated_at": "2026-11-17 14:01:54",
  "source_status": "complete"
}
```

Pada antarmuka web, tanggal dan jam pembaruan ditampilkan tepat di bawah judul grafik (`• Pembaruan snapshot: YYYY-MM-DD HH:MM:SS`).

### 4.2 Penanganan Kondisi Khusus
1. **Host Database Sumber Tidak Tersedia / Gangguan Jaringan:**
   - Generator CLI akan gagal mengeksekusi query dan membatalkan proses penerbitan snapshot.
   - Pesan error dicatat ke `writable/logs/log-YYYY-MM-DD.php` dengan prefix `[GenerateVerif0Snapshot]`.
   - Snapshot terakhir yang valid **tidak akan terhapus** dan web tetap dapat menyajikan data terakhir.
2. **Snapshot Belum Ada (Tahun Baru / Server Baru):**
   - Endpoint web merespons dengan HTTP 404 dan antarmuka web menampilkan notifikasi peringatan:
     *"Snapshot data verifikasi 0 meter untuk tahun ini belum tersedia. Silakan jalankan proses rekonsiliasi terjadwal."*
3. **Proses Sedang Berjalan (Lock Collision):**
   - Jika proses kedua dijalankan saat proses pertama masih aktif, proses kedua langsung berhenti dengan pesan:
     *"Proses rekonsiliasi tahun YYYY sedang berjalan pada proses lain."*

---

## 5. Prosedur Eksekusi Manual & Pemulihan (Troubleshooting)

### 5.1 Menjalankan Manual oleh Operator
Jika operator perlu memperbarui data grafik segera setelah adanya verifikasi besar di lapangan:
```bash
# Masuk ke direktori aplikasi
cd /var/www/html

# Eksekusi generator untuk tahun berjalan
php spark laporan:generate-verif0-snapshot

# Eksekusi generator untuk tahun tertentu (misal: 2025)
php spark laporan:generate-verif0-snapshot --tahun 2025
```

### 5.2 Pemulihan Jika Snapshot Rusak atau Tidak Terbaca
1. Hapus file snapshot yang bermasalah (jika ada):
   ```bash
   rm -f writable/snapshots/verif_0meter_2026.json
   ```
2. Pastikan tidak ada proses gantung yang mengunci:
   ```bash
   rm -f writable/snapshots/.verif_0meter_2026.lock
   ```
3. Jalankan ulang generator snapshot secara manual:
   ```bash
   php spark laporan:generate-verif0-snapshot --tahun 2026
   ```
4. Pastikan response code CLI adalah `0` dan file baru terbentuk di `writable/snapshots/`.

---

## 6. Pembuktian Kasus Khusus: Skenario Target November

Sebelum implementasi ini, laporan grafik verifikasi 0 meter memiliki anomali perhitungan yang membingungkan pengguna:

### 6.1 Masalah pada Sistem Lama
- Pada bulan November 2026 di Cabang Purwokerto 1 (`0200`), grafik lama menyajikan:
  - **Target Bulan Ini: 0**
  - **Verifikasi Bulan Ini: 129**
  - **Verifikasi Kumulatif: 2.380**
- *Penyebab Anomali Lama:* Metode lama menghitung target dengan mengurangkan total baca meter bulan sumber (1.151 baris) dengan akumulasi baris verifikasi kumulatif sebelumnya (2.251 baris). Karena $1.151 - 2.251 < 0$, sistem memaksakan target menjadi **0**. Di sisi lain, 129 baris verifikasi dicatat pada bulan November, sehingga timbul kesan janggal bahwa "target nol tetapi verifikasi bertambah".

### 6.2 Koreksi pada Snapshot Pencocokan ID Baru
Dengan metode pencocokan ID pelanggan 1:1, data Cabang Purwokerto 1 pada November 2026 terkoreksi menjadi:
- **Target Awal Bulan:** **1.151** ID pelanggan unik normal pakai 0.
- **Terverifikasi Sebelum Bulan Berjalan:** **412** ID pelanggan unik sudah diverifikasi sebelum November.
- **Target Bersih Bulan Ini:** **739** ID pelanggan unik ($1.151 - 412$).
- **Realisasi yang Cocok Bulan Ini:** **20** ID pelanggan unik target diverifikasi pada bulan November.
- **Total Aktivitas Verifikasi Lapangan:** **129** verifikasi dicatat di cabang pada bulan November (20 cocok dengan target November, 109 merupakan verifikasi akun di luar target periode terkait).

> **Kesimpulan:** Laporan kini memberikan transparansi penuh: target tersisa di awal bulan adalah 739 pelanggan, dan realisasi yang sah menyelesaikan target bulan tersebut adalah 20 pelanggan. Tidak ada lagi target yang terdistorsi menjadi nol akibat agregasi buta.
