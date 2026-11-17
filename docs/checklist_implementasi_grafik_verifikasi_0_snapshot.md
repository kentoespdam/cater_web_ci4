# Checklist implementasi snapshot grafik verifikasi 0 meter

> Beads adalah sumber status pekerjaan. Checklist ini menunjukkan urutan claim dan titik serah antar issue, bukan pengganti pembaruan status di Beads.

## Urutan claim

- [x] **1. `cater_web_ci4-b8k` - Tetapkan kontrak pencocokan dan cek kelayakan query.** Claim issue ini terlebih dahulu. Jangan mengubah kode. Catat definisi target, verifikasi yang sah, deduplikasi nomor pelanggan, batas periode, perlakuan pergantian tahun, serta strategi query lintas host. Jika status verifikasi yang sah atau aturan bisnis belum dapat dibuktikan, tandai kebutuhan keputusan pemilik produk dan jangan mengarang aturan.
- [x] **2. `cater_web_ci4-8dx` - Bangun generator CLI dan snapshot lokal.** Claim setelah `cater_web_ci4-b8k` selesai. Ikuti kontrak dan strategi query yang disetujui. Proses hanya membaca data minimum, menghitung ID unik, mengunci agar tidak ada dua proses bersamaan, dan menerbitkan hasil hanya setelah seluruh proses berhasil.
- [x] **3. `cater_web_ci4-3w5` - Alihkan API dan grafik ke snapshot.** Claim setelah `cater_web_ci4-8dx` menghasilkan snapshot valid. Pastikan request grafik membaca file lokal saja. Tampilkan realisasi yang cocok dengan target berdasarkan ID, serta waktu pembaruan snapshot. Jangan samakan aktivitas verifikasi mentah dengan realisasi target.
- [x] **4. `cater_web_ci4-0cc` - Siapkan jadwal dan panduan operasional.** Claim setelah API dan grafik memakai snapshot. Dokumentasikan pemasangan jadwal, pemantauan kesegaran, kegagalan, izin file, ruang disk, dan pemulihan. Jangan mengubah jadwal produksi tanpa prosedur deployment yang sesuai.

## Pemeriksaan serah-terima

- [x] Sebelum claim issue, jalankan `bd show <issue-id>` dan pastikan seluruh prasyaratnya selesai.
- [x] Setelah claim, ubah status issue melalui Beads dan kerjakan hanya ruang lingkup issue tersebut.
- [x] Sebelum menutup issue, pastikan kriteria selesai pada deskripsi Beads terpenuhi dan bukti/keputusan dicatat pada issue.
- [x] Tutup issue yang selesai di Beads sebelum claim issue berikutnya.
- [x] Jika aturan bisnis, akses baca, atau indeks sumber tidak dapat dipastikan, tandai issue sebagai terhambat dan catat pertanyaan spesifik. Jangan meneruskan implementasi dengan asumsi.

## Batas rancangan yang disepakati

- Perhitungan pencocokan dilakukan di proses CLI terjadwal, bukan pada request halaman.
- Snapshot final hanya menyimpan agregat dan metadata pembaruan, bukan salinan seluruh tabel atau daftar ID mentah.
- Hasil terakhir yang berhasil tetap tersedia bila proses pembaruan gagal.
- Query sumber tetap memiliki biaya baca. Strategi dan jadwal harus mengikuti bukti indeks, rencana query, beban, serta kebutuhan kesegaran data.
- SQLite tidak diperlukan untuk grafik agregat saja. Evaluasi ulang hanya jika kebutuhan filter lokal atau pemrosesan snapshot memerlukannya.

## Catatan pelaksanaan

- Implementasi harus menjalankan analisis dampak GitNexus pada simbol yang akan diubah sebelum edit, sesuai aturan repository.
- Checklist ini tidak menandakan issue telah di-claim atau selesai. Periksa status terkini di Beads.
