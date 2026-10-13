# LLM Routing & Token Rules

## LLM Routing — Mandatory Classification

Root agent WAJIB mengklasifikasikan SETIAP task ke salah satu tier berikut SEBELUM bertindak. DILARANG skip klasifikasi.

| Tier | Trigger | Model | Role Name |
|---|---|---|---|
| `[LITE]` | Baca/tulis file, jalankan command, ekstrak log, script repetitif, I/O | `flash_lite` | Lite Worker |
| `[PRO]` | Desain arsitektur, migrasi framework, debug algoritma kompleks, evaluasi keamanan, refactoring strategis | `flash` (Gemini Flash High) atau `inherit` jika root=Sonnet | Pro Reasoner |
| `[DIRECT]` | Pertanyaan trivial yang bisa dijawab 1–2 kalimat | root (inline) | — |

### Routing Rules (WAJIB diikuti)

1. **[DIRECT]**: Jawab langsung. Zero subagent spawn. Zero basa-basi — setiap token harus mengandung informasi.
2. **[LITE]**: Cek `manage_subagents(Action="list")` dulu. Jika ada Lite Worker idle → `send_message` (reuse). Jika tidak ada / killed → `invoke_subagent` model `flash_lite`, role `"Lite Worker"`.
3. **[PRO]**: DILARANG menjawab sendiri. Cek `manage_subagents(Action="list")` dulu. Jika ada Pro Reasoner idle → `send_message` (reuse). Jika tidak ada / killed → `invoke_subagent` model `inherit` (atau `flash`), role `"Pro Reasoner"`. Root HANYA membaca blok `<<<DISPATCH>>>` dari output Pro — reasoning di luar blok diabaikan.
4. **Task Ambiguous** ([LITE] + [PRO]): Split sequential — [LITE] ekstraksi dulu, hasil di-forward via ARTIFACT path ke [PRO]. Root tidak membaca konten file.
5. **Subagent reuse wajib**: Spawn baru HANYA jika subagent killed atau context penuh. Multiple Lite Workers diizinkan untuk task paralel. Maksimal 1 Pro Reasoner per sesi.

### DISPATCH Format (Pro → Root)

Pro Reasoner WAJIB mengakhiri setiap output dengan blok ini:

```
<<<DISPATCH>>>
TIER: LITE | DIRECT
TUGAS: [instruksi spesifik untuk worker atau root]
ARTIFACT: [absolute path file/scratchpad, atau NONE]
<<<END_DISPATCH>>>
```

Root agent HANYA membaca konten antara `<<<DISPATCH>>>` dan `<<<END_DISPATCH>>>`. Semua reasoning di atas blok ini diabaikan.

### Error Handling dalam Routing

- Lite Worker gagal → lapor ke root. Root retry max 3x. Setelah 3x gagal → eskalasi ke Pro Reasoner untuk root cause analysis. Setelah Pro → jika masih gagal → riset internet.
- Pro Reasoner DILARANG melakukan self-fix loop. Semua perbaikan via root.

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
