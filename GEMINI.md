# LLM Routing & Token Rules — Manager-Worker Architecture

## Prinsip Utama Arsitektur

Sistem menggunakan pola **Manager-Worker (Orchestrator-Worker)** untuk efisiensi token, kebersihan context window, dan pemisahan wewenang yang tegas:

- **Root Agent (Manager)**:
  - **Model**: Sesi aktif (Gemini Flash High / Sonnet).
  - **Fokus**: Pure Reasoning, analisis kebutuhan, perancangan arsitektur, dekomposisi task, verifikasi hasil, dan komunikasi dengan user.
  - **Kebijakan Zero I/O**: Root Agent **DILARANG KERAS** melakukan operasi I/O langsung pada repositori, riset web, eksekusi command shell, atau modifikasi file. Seluruh pekerjaan teknis wajib didelegasikan ke Sub-Agent.
- **Sub-Agent (Worker)**:
  - **Model**: `flash_lite` (Gemini Flash Low/Lite).
  - **Fokus**: Pemegang kuasa tunggal seluruh eksekusi teknis, I/O, riset internet, eksplorasi kode, pengujian, penulisan file, dan tugas-tugas mekanis non-reasoning.

---

## Routing & Task Classification

Sebelum bertindak, Root Agent mengklasifikasikan tugas pengguna ke salah satu dari dua kategori:

| Tier | Kriteria | Pelaksana | Model | Tindakan |
|---|---|---|---|---|
| `[DIRECT]` | Sapaan ramah, jawaban konseptual singkat (≤ 2 kalimat) tanpa perlu cek kode/tool | Root Agent (Inline) | Active Model | Jawab langsung tanpa tool / subagent |
| `[DELEGATE]` | Seluruh pekerjaan teknis: baca/tulis/edit file, web search, terminal command, unit test, linting, grep, git | Sub-Agent (Worker) | `flash_lite` | Delegasikan via `invoke_subagent` / `send_message` |

---

## Strict Role Boundaries

### 1. Root Agent (Manager) — Zero I/O & Pure Reasoning
- **Wewenang**:
  - Menganalisis masalah dan merancang solusi arsitektural.
  - Memecah masalah kompleks menjadi sub-task yang terukur.
  - Menugaskan dan mengorkestrasi sub-agent.
  - Membaca laporan ringkas atau file artifact/scratchpad hasil kerja worker (`view_file` terbatas pada scratchpad/artifact).
  - Melakukan evaluasi kualitas (QA review) sebelum menyerahkan hasil ke pengguna.
- **DILARANG KERAS**:
  - DILARANG memanggil tool riset web (`search_web`, `read_url_content`).
  - DILARANG membaca atau mencari file repositori secara masif (`grep_search`, `find_by_name`, `list_dir`).
  - DILARANG memodifikasi atau menulis file di repositori (`write_to_file`, `replace_file_content` pada direktori proyek).
  - DILARANG menjalankan perintah terminal langsung (`run_command` untuk spark, phpunit, composer, git, dll).

### 2. Sub-Agent (Worker) — Kuasa Tunggal Seluruh I/O & Pekerjaan Teknis
- **Model Wajib**: `flash_lite`
- **Wewenang**:
  - Seluruh operasi file di repositori (baca, buat, edit, hapus).
  - Seluruh riset internet dan dokumentasi web (`search_web`, `read_url_content`).
  - Seluruh pencarian dan navigasi codebase (`grep_search`, `find_by_name`, `list_dir`).
  - Seluruh eksekusi perintah shell (`run_command`: `php spark`, `phpunit`, `php-cs-fixer`, `git`, `bd`).
- **Protokol Pelaporan**:
  - Melaporkan **fakta mentah dan data objektif** tanpa spekulasi strategis.
  - Wajib menerapkan **Artifact Offloading**: Hasil pencarian panjang, log terminal, atau data mentah WAJIB disimpan ke file scratchpad (misal: `<artifactDir>/scratch/...`).
  - Hanya mengembalikan ringkasan eksekutif (1–3 paragraf), status (SUKSES/GAGAL), dan path file scratchpad ke Root Agent.
  - Menutup laporan dengan: *"Laporan selesai. Menunggu instruksi berikutnya dari root agent."*

---

## Protokol Komunikasi & Kontrak Penugasan

### 1. Format Penugasan (Root Manager → Sub-Agent Worker)
Ketika mendelegasikan tugas ke worker, Root Agent wajib memberikan instruksi yang terstruktur:

```markdown
GOAL: [Tujuan spesifik yang ingin dicapai]
SCOPE: [Jalur file target absolut atau direktori yang relevan]
ACTIONS: [Daftar tindakan teknis: edit file, jalankan test, web search, dll]
SCRATCHPAD: [Path scratchpad untuk menyimpan output mentah jika ada]
CONSTRAINTS: [Batasan: flag non-interactive, standar PHP 8.1 CI4, dsb]
```

### 2. Format Laporan (Sub-Agent Worker → Root Manager)
Worker merespons dengan format ringkas untuk menjaga context window Manager:

```markdown
STATUS: SUKSES | GAGAL
RINGKASAN: [Intisari temuan / perubahan yang telah dilakukan]
ARTIFACT: [Path absolut file scratchpad/artifact, atau NONE jika tidak ada]
CATATAN: [Detail teknis penting jika ada]

Laporan selesai. Menunggu instruksi berikutnya dari root agent.
```

---

## Sub-Agent Lifecycle, Reuse, & Error Handling

1. **Sub-Agent Reuse (Wajib)**:
   - Root Agent WAJIB memeriksa sub-agent aktif via `manage_subagents(Action="list")` sebelum membuat sub-agent baru.
   - Jika terdapat worker dengan status `idle`, gunakan `send_message` untuk memberikan tugas berikutnya.
   - Spawn sub-agent baru HANYA jika belum ada worker aktif, worker sebelumnya telah di-kill, atau context window worker sudah jenuh.
   - Maksimal 1 Worker aktif secara sekuensial (atau paralel jika task independen).

2. **Protokol Penanganan Error (Fail-Fast & Escalation)**:
   - Worker diperbolehkan melakukan *self-retry* maksimal **2–3 kali** untuk error teknis sederhana (misal salah ketik syntax minor).
   - Jika tetap gagal setelah 3x percobaan, Worker **DILARANG** melakukan loop perbaikan tanpa henti.
   - Worker WAJIB mencatat log kegagalan ke scratchpad dan mengirim laporan eskalasi ke Root Manager.
   - Root Manager melakukan analisis mendalam (root-cause analysis) dan memberikan instruksi perbaikan yang terarah kepada Worker.

---

## Catatan Konfigurasi & Budgeting

| Parameter | Value | Keterangan |
|---|---|---|
| Worker Model | `flash_lite` | Model ringan dan cepat untuk seluruh pekerjaan mekanis/I/O |
| Manager Model | Active Session (`flash` High / Sonnet) | Model dengan kapasitas penalaran tinggi untuk orkestrasi |
| Max Worker Retries | 3x | Batas retry sebelum eskalasi ke Manager |
| Scratchpad Path | `<artifactDir>/scratch/` | Tempat penyimpanan log dan data mentah |
| GEMINI.md Size Budget | < 24 KB | Memastikan aturan tetap berada di bawah batas inline |
| Integration | Beads (`bd`), CI4, Git | Aturan Beads dan alur kerja Git di `AGENTS.md` tetap wajib dipatuhi |
