# Project Rules for AI Agents: cater_web_ci4 (Antigravity 2.0 Optimized)

## Konteks Project

Project **cater_web_ci4** adalah Sistem Monitoring & Verifikasi Catat Meter Air (PDAM) berbasis web. Menggunakan framework **CodeIgniter 4** (`v4.5.1+`) dengan **PHP 8.1+**, PHPUnit 10, PHP-CS-Fixer 3, dan Composer.
Referensi utama: [`CODING_RULES.md`](./CODING_RULES.md) untuk standar arsitektur CI4, PHP 8.1+, validasi, keamanan, dan testing.

---

## 1. Adaptive Routing & Agent Roles (Direct-First)

Apply this strict priority order before executing any task:

### Priority 1: Mandatory Deep-Reasoning (`deep_reasoner` Sub-Agent)
Invoke `invoke_subagent` with `deep_reasoner` (tier: `pro`) BEFORE writing code when ANY of these objective conditions is met:
- **Auth/Security:** Menyentuh logic autentikasi, session, filter, atau middleware di `app/Filters/` atau `app/Controllers/Auth.php` — SELALU.
- **Service Layer Redesign:** Refactor yang mengubah signature publik Service class di `app/Services/`.
- **Database Schema:** Perubahan migration atau relasi antar tabel yang berdampak luas.
- **Stuck Bug:** Bug yang tidak terjelaskan setelah tepat 2 attempt debugging di level Root Agent.

*Exceptions (Do NOT invoke `deep_reasoner`):*
- Implementing an already-confirmed Implementation Contract from a prior `deep_reasoner` response.
- Writing routine unit tests (`phpunit`) or fixing localized bugs with an already-identified root cause.
- Task yang tidak menyentuh file `Auth.php`, `Services/`, atau migration sama sekali.

**Quota Fallback:** Jika `deep_reasoner` (model: `pro`) mengembalikan error quota/rate-limit, WAJIB segera re-invoke dengan `deep_reasoner_fallback` (model: `flash`, Gemini Flash High). Gunakan prompt dan context yang identik. Jika fallback mengembalikan `Confidence Level: LOW`, tunda implementasi dan laporkan ke user untuk keputusan manual.

### Priority 2: Direct Execution (Root Agent — No Sub-Agent)
Root Agent MUST execute directly without spawning any sub-agent when:
- Running issue tracker operations (`bd`), `git` commands, or single `gitnexus` queries.
- Making localized code edits (≤ 2 files, < 50 LOC) pada file selain `Auth.php`/`Services/`/migration, mengikuti pola yang sudah ada.
- Running final verification commands (`phpunit`, `php-cs-fixer`).
- Responding to questions, writing docs, or analysis without code changes.

### Priority 3: Substantial Technical Delegation (`flash_lite` Sub-Agent)
Invoke `invoke_subagent` with `flash_lite` (tier: `flash`) when:
- Implementation spans ≥ 2 files with substantial changes (new endpoints, new modules, new Controllers).
- Creating a test suite with ≥ 3 test cases.
- Broad codebase exploration requiring reads across > 5 files.
- Any task estimated to take > 15 minutes of execution time.

---

## 2. Sub-Agent Execution, Error Handling & Reporting Protocol

### Bounded Self-Fix (Anti Ping-Pong)
- Sub-agent DIIZINKAN melakukan perbaikan mandiri (*self-fix*) maksimal **2 kali percobaan lokal** khusus untuk error deterministik (syntax error, missing class, temuan `php-cs-fixer`, atau kegagalan test dengan penyebab jelas).
- Jika masih gagal setelah 2 percobaan lokal, atau jika menemukan ambiguitas desain/pelanggaran constraint keamanan, sub-agent WAJIB berhenti dan melapor ke Root Agent.
- Jika error berlanjut setelah 3 percobaan remediasi di level Root Agent, lakukan riset referensi terbaru (dokumentasi CI4, PHPUnit, PHP-CS-Fixer).

### Proportional Reporting Protocol (STRICT)

**A. Untuk Tugas Eksplorasi / Riset (Tanpa Modifikasi File):**
Laporkan temuan secara padat dalam maksimal 5 baris berisi poin inti dan referensi `file:baris`. Akhiri dengan: `"Report complete. Awaiting next instructions."`

**B. Untuk Tugas Modifikasi Kode / Eksekusi Teknis:**
Sub-agent wajib melaporkan dalam format berlapis berikut:

```text
STATUS: [DONE|PARTIAL|BLOCKED|ERROR]
HASIL: <1–3 kalimat inti — apa yang berubah, ditemukan, atau gagal, dan mengapa penting>

ARTEFAK TERMODIFIKASI:
- <file>:<baris-dari>–<baris-ke> — <deskripsi singkat perubahan>
  CONSTRAINT CHECK: <PASS/FAIL + alasan singkat terhadap: strict_types, SQL injection, XSS escape, CSRF>

VERIFIKASI:
- Tests: <X passed, Y failed — sebutkan nama test spesifik yang relevan; nyatakan eksplisit jika 0 test>
- Lint/Type: <PASS / FAIL: <baris pesan error spesifik dari php-cs-fixer>>
- Regression: <Ya/Tidak + detail singkat>

Report complete. Awaiting next instructions.
```

**Aturan Ketat Laporan Modifikasi:**
- **Wajib sebutkan `file:baris`** untuk setiap perubahan dan **wajib jalankan Constraint Check**.
- Buang *stack trace* penuh atau listing kode panjang—ringkas hanya ke baris error spesifik (< 10 baris raw output).
- DILARANG menyertakan saran, opini, langkah selanjutnya, atau mengeksekusi pekerjaan di luar scope.
- DILARANG melaporkan `STATUS: DONE` jika ada test `FAIL`, lint `FAIL`, atau constraint check `FAIL`.
- Root Agent WAJIB memeriksa bagian `ARTEFAK TERMODIFIKASI` dan `VERIFIKASI` sebelum mengambil keputusan berikutnya (jangan hanya membaca `STATUS`).

---

## 3. Token Economy & Pre-Coding Gates

- **No Preamble & Concise Output:** Hilangkan kalimat basa-basi ("Baik", "Tentu saja", "Saya akan..."). Mulai langsung dari substansi.
- **Selective Reading & Micro-Diffs:** Baca hanya rentang baris yang relevan (hindari membaca seluruh file >150 baris jika posisi simbol sudah diketahui). Edit hanya baris yang berubah.
- **Quiet Commands:** Selalu gunakan flag minim output pada terminal (`-q`, `--quiet`, `head`, `grep`).
- **GitNexus Targeted Gate:**
  - WAJIB gunakan `gitnexus_query` atau `gitnexus_impact` sebelum mengeksplorasi atau memodifikasi Controller, Service, Model, dan kontrak antar-modul.
  - Untuk CLI `npx gitnexus`, selalu sertakan `--repo cater_web_ci4` (contoh: `npx gitnexus query "BacaMeterService" --repo cater_web_ci4`), kecuali untuk perintah `npx gitnexus analyze`.
- **Conditional Skill Gate:**
  - **Ponytail:** WAJIB load skill `/ponytail` sebelum memodifikasi kode produksi (lewati jika hanya melakukan query `bd`, `git`, atau diskusi desain).
  - **Grilling Rules:** WAJIB ikuti `GRILLING_RULES.md` saat melakukan Q&A interaktif dan penajaman arsitektur (Prioritas 1).
- **Issue Tracking (Beads/`bd`):** Klaim task (`bd update <id> --claim`), tutup task (`bd close <id>`).
- **Sandbox Policy:** WAJIB gunakan `BypassSandbox: true` untuk semua eksekusi perintah `git` dan `bd`.

---

## 4. File Size, PHP Toolchain & Domain Rules (`cater_web_ci4`)

### Modularitas & Ukuran File
- Target: **150–250 LOC** per file.
- Hard Ceiling: **Maksimal 300 LOC**. WAJIB pecah/modularisasi jika mendekati batas.
- Struktur modul CI4: `app/Controllers/` (tipis), `app/Services/` (logika bisnis), `app/Models/` (akses data), `app/Filters/` (middleware).

### PHP Toolchain (Composer Wajib)
Seluruh eksekusi PHP WAJIB menggunakan perintah berikut:
```bash
php spark <command>                        # CLI CodeIgniter 4
./vendor/bin/phpunit --testdox -q          # Jalankan test (mode ringkas)
vendor/bin/php-cs-fixer fix src/ --dry-run # Lint check (dry run)
vendor/bin/php-cs-fixer fix app/           # Lint fix
composer test                              # Alias test suite
```

### Keamanan & Domain Constraints (Harga Mati)
1. **Strict Types Wajib:** Setiap file PHP baru di `app/` WAJIB diawali `declare(strict_types=1);`.
2. **Anti SQL Injection:** DILARANG query string concatenation. WAJIB gunakan Query Builder CI4 atau prepared statement.
3. **XSS Escape:** Semua output ke view WAJIB di-escape via `esc()` atau `htmlspecialchars()`. DILARANG echo raw user input.
4. **CSRF Aktif:** Form POST wajib menyertakan CSRF token. JANGAN nonaktifkan filter CSRF untuk route produksi.
5. **Autentikasi & Otorisasi:** Validasi sesi/role WAJIB di Filter, bukan di dalam Controller logic.
6. **Integritas Data Meter:** Nilai baca meter dan kalkulasi tagihan SELALU diambil/dihitung *server-side*. Klien TIDAK BOLEH mengirim nilai yang langsung digunakan tanpa validasi ulang.

---

## 5. Prosedur Penyelesaian Pekerjaan (Completion Workflow)

Sebelum menyatakan task selesai secara keseluruhan:
1. Jalankan `gitnexus_detect_changes()` untuk memverifikasi cakupan perubahan.
2. Jalankan `./vendor/bin/phpunit -q` — pastikan seluruh test yang relevan lulus (hijau).
3. Jalankan `vendor/bin/php-cs-fixer fix app/ --dry-run` — pastikan bebas pelanggaran PSR-12.
4. Push perubahan ke remote (WAJIB dengan `BypassSandbox: true`):
   ```bash
   git pull --rebase
   bd dolt push
   git push
   git status  # HARUS menunjukkan "up to date with origin"
   ```
5. Perbarui atau tutup status issue di `bd` (`bd close <id>`).