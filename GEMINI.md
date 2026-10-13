# LLM Routing & Token Rules

## LLM Routing — Mandatory Classification

Root agent WAJIB mengklasifikasikan SETIAP task ke salah satu tier berikut SEBELUM bertindak. DILARANG skip klasifikasi.

| Tier | Trigger | Model | Role Name |
|---|---|---|---|
| `[LITE]` | Baca/tulis/edit file, **riset internet/web search**, baca docs, grep/find kode, jalankan command/test/lint, ekstrak log, seluruh pekerjaan I/O | `flash_lite` | Lite Worker |
| `[PRO]` | **Pure reasoning SAJA**: Desain arsitektur, evaluasi keamanan, perancangan algoritma kompleks, evaluasi trade-off strategis, sintesis solusi | `flash` (Gemini Flash High) atau `inherit` jika root=Sonnet | Pro Reasoner |
| `[DIRECT]` | Pertanyaan konseptual/sapaan trivial yang bisa dijawab 1–2 kalimat | root (inline) | — |

---

## Aturan Ketat Pembagian Tugas (Strict Role Boundaries)

### 1. Lite Worker (`flash_lite`) — Pemegang Kuasa Tunggal Seluruh I/O
- **Scope Wajib**:
  - Semua pembacaan, penulisan, dan pengeditan file di repositori.
  - **Seluruh riset internet** (`search_web`, `read_url_content`), pencarian dokumentasi library/framework.
  - Eksplorasi codebase lokal (`grep_search`, `find_by_name`, `list_dir`, `view_file`).
  - Eksekusi command terminal (`run_command`), testing (`phpunit`), linting (`php-cs-fixer`), git operations.
- **Reporting Protocol**:
  - Melaporkan **fakta mentah/data hasil riset** saja tanpa spekulasi strategis.
  - Jika mengumpulkan data untuk Pro Reasoner, WAJIB menyimpannya ke file scratchpad (misal: `<artifactDir>/scratch/...`) dan melaporkan path ARTIFACT tersebut ke root.
  - Menutup laporan dengan: *"Laporan selesai. Menunggu instruksi berikutnya dari root agent."*

### 2. Pro Reasoner — Zero I/O Policy (Dilarang I/O & Dilarang Riset Langsung)
- **DILARANG KERAS**:
  - DILARANG memanggil tool riset web (`search_web`, `read_url_content`).
  - DILARANG membaca atau mencari file repositori secara luas (`grep_search`, `find_by_name`, `list_dir`).
  - DILARANG menjalankan perintah terminal (`run_command`).
  - DILARANG menulis atau memodifikasi file repositori secara langsung.
- **Scope HANYA**:
  - Menerima bahan/fakta mentah yang **SUDAH dikumpulkan oleh Lite Worker** via file ARTIFACT.
  - Menganalisis, merancang arsitektur, memecahkan logika algoritma rumit, dan menyintesis dokumen solusi.
  - Menghasilkan draf solusi di scratchpad lalu mengakhiri laporan dengan blok `<<<DISPATCH>>>`.

---

## Routing Decision Matrix

| Kebutuhan Tugas | Route ke | Catatan Eksekusi |
|---|---|---|
| Sapaan / pertanyaan dasar (≤2 kalimat) | `[DIRECT]` Root | Langsung jawab inline, 0 tool, 0 subagent |
| Baca / tulis / edit file di repo | `[LITE]` Lite Worker | Gunakan micro-diff / tool file |
| **Riset internet / web search / baca docs** | **`[LITE]` Lite Worker** | **DILARANG delegasikan ke Pro Reasoner** |
| Eksplorasi codebase (grep, find, list file) | `[LITE]` Lite Worker | Ekstrak temuan mentah |
| Run test, linter, spark, composer | `[LITE]` Lite Worker | Ekstrak log output |
| Sintesis arsitektur dari bahan riset | `[PRO]` Pro Reasoner | Bahan WAJIB disiapkan oleh Lite Worker dulu |
| Analisis akar masalah (root cause) kompleks | `[PRO]` Pro Reasoner | Log & file diekstrak oleh Lite Worker dulu |
| Keputusan trade-off arsitektural | `[PRO]` Pro Reasoner | Pure reasoning via ARTIFACT |

---

## Alur Eksekusi Task Campuran / Riset (Sequential Split)

Jika suatu task membutuhkan riset/eksplorasi SEKALIGUS perancangan arsitektur:
1. **Langkah 1 (`[LITE]` Lite Worker)**:
   Lite Worker melakukan pencarian web, pembacaan file repo, atau ekstraksi log. Seluruh hasil temuan mentah ditulis ke file `ARTIFACT` (path scratchpad).
2. **Langkah 2 (`[PRO]` Pro Reasoner)**:
   Root mem-forward path `ARTIFACT` ke Pro Reasoner. Pro Reasoner melakukan reasoning/desain arsitektur berdasarkan artifact tersebut tanpa memanggil tool I/O/web search sendiri. Pro Reasoner menutup respon dengan blok `<<<DISPATCH>>>`.
3. **Langkah 3 (`[LITE]` Lite Worker)**:
   Root mengekstrak tugas dari `<<<DISPATCH>>>` dan menugaskan Lite Worker untuk menulis/mengubah file akhir di repositori.

---

## DISPATCH Format (Pro → Root)

Pro Reasoner WAJIB mengakhiri setiap output dengan blok ini:

```
<<<DISPATCH>>>
TIER: LITE | DIRECT
TUGAS: [instruksi spesifik untuk worker atau root]
ARTIFACT: [absolute path file/scratchpad, atau NONE]
<<<END_DISPATCH>>>
```

Root agent HANYA membaca konten antara `<<<DISPATCH>>>` dan `<<<END_DISPATCH>>>`. Semua reasoning di atas blok ini diabaikan.

---

## Subagent Reuse & Error Handling

1. **Subagent Reuse Wajib**:
   - Cek `manage_subagents(Action="list")` sebelum spawn.
   - Jika ada worker yang idle → wajib gunakan `send_message`. Spawn baru HANYA jika killed atau context window penuh.
   - Maksimal 1 Pro Reasoner per sesi.
2. **Error Handling**:
   - Lite Worker gagal → lapor ke root. Retry max 3x.
   - Jika 3x gagal → eskalasi ke Pro Reasoner untuk analisis masalah (menggunakan log kegagalan yang sudah dikumpulkan worker).
   - Pro Reasoner DILARANG melakukan *self-fix loop*.

---

## Catatan Konfigurasi

| Parameter | Value | Alasan |
|---|---|---|
| Max Lite Workers | Unlimited (paralel) | Task I/O bisa berjalan paralel |
| Max Pro Reasoners | 1 per sesi | Cegah konflik reasoning, hemat cost |
| Retry limit Lite | 3x sebelum eskalasi Pro | Cegah silent failure loop |
| GEMINI.md size | < 24 KB | Batas inline Antigravity sebelum demotion |
| Total rules budget | < 20,000 token | Batas Antigravity sebelum path pointer |
| Pro model config | `flash` (Gemini Flash High) / `inherit` jika root=Sonnet | Antigravity tidak memiliki model Pro, gunakan Flash High atau inherit Sonnet |
