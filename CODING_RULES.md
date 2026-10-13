# CODING RULES & ARCHITECTURAL GUIDELINES
**Project**: `cater_web_ci4` (Sistem Monitoring & Verifikasi Catat Meter Air / PDAM)  
**Framework**: CodeIgniter 4 (`v4.5.1+`)  
**PHP Version**: PHP `8.1+`  
**Tooling**: PHPUnit 10, PHP-CS-Fixer 3, Composer  

---

## 1. Prinsip Utama & Filosofi Desain

Dokumen ini adalah standar resmi pengembangan software pada proyek **`cater_web_ci4`**. Seluruh developer dan agen AI wajib mematuhi aturan ini untuk menjamin integritas, keterbacaan, keamanan, dan kemudahan pengujian (*testability*) kode.

### Prinsip Pokok:
1. **Strict & Type-Safe First**: Memaksimalkan fitur PHP 8.1+ (strict types, typed properties, return types, enums, match expression).
2. **Separation of Concerns (Anti Fat-Controller)**: Controller tipis, Service Layer menangani logika bisnis, Model fokus pada akses data, Entity memegang representasi domain.
3. **Defense in Depth (Security by Design)**: Tidak ada query SQL tanpa binding/prepared statement, validasi input berbasis array strict, output wajib diescape sesuai konteks, dan CSRF selalu aktif.
4. **Automated Verification**: Setiap logic baru harus dapat diuji via PHPUnit dan lolos linter PHP-CS-Fixer (PSR-12).

---

## 2. Standar Bahasa PHP 8.1+ & PSR-12

### 2.1 Deklarasi Strict Types Wajib
Setiap file PHP (`app/`, `tests/`) **wajib** diawali dengan deklarasi strict types tepat setelah tag pembuka PHP:

```php
<?php

declare(strict_types=1);

namespace App\Controllers;
```

### 2.2 Explicit Property & Return Type Hints
- DILARANG menulis method atau fungsi tanpa parameter type hint dan return type hint eksplisit.
- DILARANG menggunakan properti dinamis (deprecated sejak PHP 8.2). Semua properti kelas harus dideklarasikan di awal kelas.

```php
// SALAH (Implicit types, dynamic property)
class HasilBacaService {
    public function getSummary($periode) {
        $this->total = 100;
        return $this->total;
    }
}

// BENAR (PHP 8.1 Strict Types)
final class HasilBacaService
{
    private int $totalCount = 0;

    public function getSummary(string $periode): array
    {
        $this->totalCount = 100;
        return ['total' => $this->totalCount];
    }
}
```

### 2.3 Memanfaatkan Fitur Modern PHP 8.1+

#### A. Readonly Properties
Gunakan `readonly` untuk dependencies yang diinjeksi via konstruktor atau pada Data Transfer Objects (DTO) yang tidak boleh bermutasi:

```php
namespace App\Services;

use App\Models\BacaMeterModel;

final class BacaMeterService
{
    public function __construct(
        private readonly BacaMeterModel $bacaMeterModel,
    ) {}
}
```

#### B. Backed Enums untuk Status & Tipe Data Terbatas
Ganti konstanta angka/string liar (`1`, `2`, `'approved'`, `'gagal'`) dengan PHP 8.1 Backed Enums:

```php
namespace App\Enums;

enum StatusVerifikasi: int
{
    case BELUM_DICEK = 0;
    case CEK_CABANG = 1;
    case CEK_KOPERASI = 2;
    case GAGAL = 3;

    public function label(): string
    {
        return match ($this) {
            self::BELUM_DICEK => 'Belum Dicek',
            self::CEK_CABANG => 'Cek Cabang',
            self::CEK_KOPERASI => 'Cek Koperasi',
            self::GAGAL => 'Gagal / Perlu Ulang',
        };
    }
}
```

#### C. Match Expressions menggantikan Switch
Gunakan ekspresi `match` yang strict-comparison dan return-expression:

```php
// Mengonversi kode status ke CSS badge class
$badgeClass = match ($status) {
    StatusVerifikasi::CEK_CABANG => 'badge-success',
    StatusVerifikasi::CEK_KOPERASI => 'badge-info',
    StatusVerifikasi::GAGAL => 'badge-danger',
    default => 'badge-secondary',
};
```

#### D. Nullsafe Operator (`?->`)
Hindari pengecekan bertingkat `isset()` atau `!empty()` ketika mengakses properti relasional:

```php
// SEBELUM
$namaPetugas = ($user !== null && $user->petugas !== null) ? $user->petugas->nama : 'Unknown';

// SESUDAH
$namaPetugas = $user?->petugas?->nama ?? 'Unknown';
```

---

## 3. Arsitektur Berlapis: Controller - Service - Model - Entity

```
   [ HTTP Request ]
          │
          ▼
   [ App\Filters ] ────────► (AuthFilter, CSRF, SecureHeaders)
          │
          ▼
 [ App\Controllers ] ──────► Validasi format HTTP, baca Query/Body, panggil Service, kembalikan Response
          │
          ▼
   [ App\Services ] ───────► Logika bisnis, kalkulasi meter, transaksi database, ekspor file
          │
          ▼
   [ App\Models ] ─────────► Query Builder dengan Prepared Statements & Scopes
          │
          ▼
  [ App\Entities ] ────────► Representasi baris database, mutators, getters, casting
```

### 3.1 Controller Rules (`app/Controllers/`)
1. **Thin Controller**: Controller hanya bertugas menangani siklus HTTP (menerima input, memanggil service, mengembalikan output).
2. **Tidak Ada Query Database di Controller**: Dilarang instansiasi `$db->query(...)` atau memanggil logika kalkulasi berat di controller.
3. **Response Standardization**:
   - Untuk Web HTML: Selalu return `string` (dari fungsi `view()`) atau `ResponseInterface` (dari `redirect()`).
   - Untuk REST API: Selalu return `ResponseInterface` (via `$this->response->setJSON(...)` atau `ResponseTrait`).
4. **Gunakan Redirect Method yang Benar**:
   - SALAH: `return redirect("auth");`
   - BENAR: `return redirect()->to('/auth');` atau `return redirect()->route('login');`

#### Contoh Controller yang Sesuai Standar:
```php
<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\HasilBacaService;
use CodeIgniter\HTTP\ResponseInterface;

final class HasilBaca extends BaseController
{
    public function __construct(
        private readonly HasilBacaService $service = new HasilBacaService(),
    ) {}

    public function index(): ResponseInterface
    {
        $periode = (string) $this->request->getGet('periode');
        if ($periode === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'Parameter periode wajib diisi (format: YYYY-MM).',
            ]);
        }

        $result = $this->service->getRingkasanHasilBaca($periode);

        return $this->response->setJSON($result);
    }
}
```

---

### 3.2 Service Layer (`app/Services/`)
CodeIgniter 4 tidak menyediakan folder `Services` domain secara default, tetapi proyek `cater_web_ci4` mewajibkan folder ini untuk menampung seluruh logika bisnis.

- Lokasi: `app/Services/`
- Tanggung jawab:
  - Perhitungan progress baca meter, verifikasi selisih angka meter.
  - Penggabungan data lintas model (misal `BacaMeterModel` + `CustModel` Sikompak).
  - Manajemen database transaction (`transStart()`, `transComplete()`).
  - Caching logic kompleks.

#### Contoh Service Layer:
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BacaMeterModel;
use App\Models\Sikompak\CustModel;
use App\Libraries\MyDate;
use CodeIgniter\Database\Exceptions\DatabaseException;

final class HasilBacaService
{
    public function __construct(
        private readonly CustModel $custModel = new CustModel(),
        private readonly BacaMeterModel $bacaMeterModel = new BacaMeterModel(),
    ) {}

    /**
     * @return array{total: int, rows: array<int, mixed>, footer: array<string, mixed>}
     */
    public function getRingkasanHasilBaca(string $periode): array
    {
        $myDate = MyDate::withPeriode($periode);
        $startDate = $myDate->getStartDate();
        $endDate = $myDate->getEndDate();
        $cacheKey = sprintf('hasil_baca_%s_%s', $startDate, $endDate);

        if (($cached = cache($cacheKey)) !== null) {
            return $cached;
        }

        $customerData = $this->custModel->getTotalPelangganPerPembaca();
        $meterReadData = $this->bacaMeterModel->getJumlahCekData($startDate, $endDate);

        $rows = $this->aggregateData($customerData, $meterReadData);
        $footer = $this->calculateFooter($rows);

        $result = [
            'total'  => count($rows),
            'rows'   => $rows,
            'footer' => $footer,
        ];

        cache()->save($cacheKey, $result, 120);

        return $result;
    }

    private function aggregateData(array $customers, array $meterReads): array
    {
        // Business logic agregasi murni, mudah di-unit-test secara terisolasi
        return [];
    }

    private function calculateFooter(array $rows): array
    {
        return [];
    }
}
```

---

### 3.3 Model Rules (`app/Models/`)
1. Model **wajib** mendefinisikan properti konfigurasi:
   - `protected $table`
   - `protected $primaryKey`
   - `protected $returnType` (sangat direkomendasikan mengarah ke `Entity::class` daripada `object` generik atau `array`)
   - `protected $allowedFields` (wajib dideklarasikan eksplisit untuk mencegah mass assignment vulnerability)
2. **DILARANG KERAS merangkai string SQL dengan variabel mentah**. Gunakan parameter binding atau Query Builder.

#### Refactoring Query Model yang Rentan (Anti-Pattern vs Best-Practice):
```php
// ==========================================
// SALAH: Rawan SQL Injection & String Interpolation
// ==========================================
public function getJumlahCekDataBad($tglAwal, $tglAkhir)
{
    return $this->select("petugas, COUNT(no_sam) AS jml_baca")
        ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'") // BAHAYA!
        ->groupBy("petugas")
        ->findAll();
}

// ==========================================
// BENAR: Menggunakan Query Builder Parameter Binding
// ==========================================
public function getJumlahCekData(string $tglAwal, string $tglAkhir): array
{
    return $this->select('petugas, COUNT(no_sam) AS jml_baca, 
            SUM(CASE WHEN cek IN (1, 2, 3) THEN 1 ELSE 0 END) AS cek_koperasi,
            SUM(CASE WHEN cek = 1 THEN 1 ELSE 0 END) AS cek_cabang,
            SUM(CASE WHEN cek = 3 THEN 1 ELSE 0 END) AS gagal')
        ->where('tgl >=', $tglAwal)
        ->where('tgl <=', $tglAkhir)
        ->groupBy('petugas')
        ->orderBy('petugas', 'ASC')
        ->findAll();
}
```

---

### 3.4 Entity Rules (`app/Entities/`)
Gunakan Entity untuk membungkus satu baris record tabel database.
- Lokasi: `app/Entities/`
- Manfaat: Menyediakan tipe data yang jelas, encapsulation, casting otomatis, dan business getter/mutator.

```php
<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserWeb extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [
        'uid'       => 'integer',
        'kd_status' => 'integer',
    ];

    /**
     * Mutator: Selalu hash password saat di-set jika belum berupa bcrypt hash
     */
    public function setEncryptedPassword(string $password): self
    {
        if (! str_starts_with($password, '$2y$')) {
            $this->attributes['encrypted_password'] = password_hash($password, PASSWORD_BCRYPT);
        } else {
            $this->attributes['encrypted_password'] = $password;
        }

        return $this;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->attributes['encrypted_password'] ?? '');
    }
}
```

---

## 4. Validasi Form & Input Handling

1. Gunakan method `$this->validateData($data, $rules)` (standar modern CI4).
2. Tulis aturan validasi dalam bentuk **Array Rules**, bukan string pipa (`required|min_length[3]`), untuk menghindari kesalahan interpretasi karakter delimiter atau injection.
3. Contoh pola validasi yang benar di controller:

```php
$rules = [
    'username' => [
        'label' => 'Username',
        'rules' => ['required', 'trim', 'min_length[3]', 'max_length[50]'],
    ],
    'password' => [
        'label' => 'Password',
        'rules' => ['required', 'min_length[6]'],
    ],
];

if (! $this->validateData((array) $this->request->getPost(), $rules)) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}
```

4. Pisahkan rules yang dipakai berulang ke `app/Config/Validation.php`.

---

## 5. Standar Keamanan Aplikasi (Defense in Depth)

### 5.1 Proteksi CSRF (Cross-Site Request Forgery)
1. **CSRF Filter Aktif**: Pastikan CSRF aktif pada request form non-GET di `app/Config/Filters.php`:
   ```php
   public array $globals = [
       'before' => [
           'csrf',
           'auth',
       ],
   ];
   ```
2. **Form HTML**: Wajib menyertakan helper `<?= csrf_field() ?>` pada semua tag `<form method="post">`.
3. **AJAX Request**: Sisipkan token pada header HTTP:
   ```html
   <meta name="<?= csrf_header() ?>" content="<?= csrf_hash() ?>" id="csrf-token-meta">
   ```
   ```javascript
   fetch('/api/master/user', {
       method: 'POST',
       headers: {
           'Content-Type': 'application/json',
           'X-CSRF-TOKEN': document.getElementById('csrf-token-meta').content
       },
       body: JSON.stringify(payload)
   });
   ```
4. **Pengecualian CSRF**: Jika ada rute API yang diakses oleh aplikasi Android lapangan (`cater_mobile`), kecualikan secara spesifik pada `app/Config/Filters.php` di parameter `$filters['csrf']['except'] = ['api/android/*']` dengan autentikasi berbasis Token/JWT.

### 5.2 Pencegahan XSS (Cross-Site Scripting)
1. **Escape on Output**: Selalu escape output pada view menggunakan helper `esc()` dengan konteks yang tepat:
   ```html
   <!-- Konteks HTML (Default) -->
   <td><?= esc($user->nama_lengkap) ?></td>

   <!-- Konteks Atribut HTML -->
   <input type="text" name="cabang" value="<?= esc($user->cabang, 'attr') ?>">

   <!-- Konteks JavaScript Inline -->
   <script>
       const currentUser = <?= json_encode(esc($user->username, 'js')) ?>;
   </script>
   ```
2. Dilarang menggunakan raw `echo $variable` untuk input yang bersumber dari user/database tanpa sanitasi output.

### 5.3 Keamanan Password & Kredensial
1. **Dilarang Plaintext / MD5**: DILARANG menyimpan atau membandingkan password dengan plaintext ataupun hash usang (`md5`, `sha1`).
2. Gunakan `password_hash($password, PASSWORD_BCRYPT)` dan `password_verify($password, $hash)`.
3. **Secret Management**:
   - Seluruh credential database, host, base URL, dan encryption key WAJIB disimpan di `.env`.
   - `.env` DILARANG di-commit ke Git repository (masuk dalam `.gitignore`).
   - Sediakan file template `.env.example` yang selalu mutakhir tanpa nilai credential asli.

---

## 6. Spesifik Domain `cater_web_ci4`: Foto Meter & Ekspor Spreadsheet

### 6.1 Manajemen Foto Meter Air (`CekFoto`, `FotoMeter`)
1. **Pencegahan Path Traversal**: Saat membaca nama file foto dari request user (`$filename`), wajib memvalidasi menggunakan `basename()` dan memastikan file berada dalam direktori yang diizinkan:
   ```php
   $cleanFilename = basename($requestedFilename);
   $targetPath = WRITEPATH . 'uploads/foto_meter/' . $cleanFilename;
   $realTarget = realpath($targetPath);
   $baseFolder = realpath(WRITEPATH . 'uploads/foto_meter/');

   if ($realTarget === false || ! str_starts_with($realTarget, $baseFolder)) {
       throw new \CodeIgniter\Exceptions\PageNotFoundException('File tidak ditemukan atau akses ditolak.');
   }
   ```
2. **Content-Type & Caching Header**: Kirim header HTTP yang tepat (`Content-Type: image/jpeg`) dan Cache-Control untuk menghemat bandwidth server pembacaan foto meter.

### 6.2 Ekspor Spreadsheet (`PhpSpreadsheet`)
1. Gunakan memory buffering dan streaming langsung ke output untuk mencegah memori server membengkak (*out of memory*) pada dataset pembacaan ribuan pelanggan:
   ```php
   $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
   
   return $this->response
       ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
       ->setHeader('Content-Disposition', 'attachment;filename="laporan_meter_' . date('Ymd') . '.xlsx"')
       ->setHeader('Cache-Control', 'max-age=0')
       ->setBody(function () use ($writer) {
           $writer->save('php://output');
       });
   ```
2. Jangan menumpuk objek spreadsheet yang tidak diperlukan dalam memori; panggil `$spreadsheet->disconnectWorksheets()` jika memproses batch data besar.

---

## 7. Standar Pengujian Otomatis (Automated Testing via PHPUnit 10)

Proyek menggunakan **PHPUnit 10.5+** yang terintegrasi dengan testing suite CodeIgniter 4.

### 7.1 Struktur Folder Testing
```
tests/
├── _support/             # Test helpers, mocks, seeders
├── database/            # Database migration/seed tests
├── feature/             # HTTP & Controller integration tests
└── unit/                # Unit test murni (Libraries, Services, Entities)
```

### 7.2 Aturan Menulis Test
1. Kelas test wajib menggunakan modifier `final` dan extend `CodeIgniter\Test\CIUnitTestCase`.
2. Nama class berakhiran `Test` (contoh: `HasilBacaServiceTest.php`), nama method diawali `test*` dengan return type `: void`.
3. Gunakan assertions spesifik (`assertSame()`, `assertCount()`, `assertNull()`) daripada assertion ambigu (`assertEquals(true, ...)`).

#### Contoh Unit Test Bersih:
```php
<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Libraries\FlattenByKondisi;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class FlattenByKondisiTest extends CIUnitTestCase
{
    private array $mockBranches;
    private array $mockConditions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockBranches = [
            (object) ['id_cabang' => '0200', 'nm_cabang' => 'Cabang Purwokerto 1'],
        ];
        $this->mockConditions = [
            (object) ['kondisi' => 'normal'],
        ];
    }

    public function testCalculationCalculatesCorrectly(): void
    {
        $data = [
            (object) ['satker' => '0200', 'kondisi' => 'normal', 'total' => 15],
        ];

        $flatten = new FlattenByKondisi($data, '2026-10', $this->mockBranches, $this->mockConditions);
        $rows = $flatten->get();

        $this->assertCount(1, $rows);
        $this->assertSame(15, $rows[0]['total']);
        $this->assertSame(15, $rows[0]['normal']);
    }
}
```

---

## 8. Standar Code Style & Konfigurasi PHP-CS-Fixer

Proyek mengikuti standar **PSR-12** dengan perbaikan modern PHP 8.1.

### 8.1 File Konfigurasi `.php-cs-fixer.dist.php`
Pastikan file `.php-cs-fixer.dist.php` di root repository memiliki konfigurasi berikut:

```php
<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ])
    ->exclude([
        'Views',
        'ThirdParty',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'declare_strict_types' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try'],
        ],
    ])
    ->setFinder($finder);
```

### 8.2 Command Line Perintah Linting & Testing
- Menjalankan uji otomatis:
  ```bash
  composer test
  # atau
  ./vendor/bin/phpunit
  ```
- Memeriksa code style tanpa mengubah file (dry-run):
  ```bash
  ./vendor/bin/php-cs-fixer fix --dry-run --diff
  ```
- Memperbaiki code style secara otomatis:
  ```bash
  ./vendor/bin/php-cs-fixer fix
  ```

---

## 9. Developer & Agent Checklist Sebelum Commit

Sebelum melakukan commit atau dispatch pull request, pastikan setiap poin berikut telah terpenuhi:

- [ ] File PHP baru menyertakan `declare(strict_types=1);`.
- [ ] Semua method memiliki return type hint dan argument type hints yang valid.
- [ ] Tidak ada raw SQL string concatenation (`where("col = '$val'")`). Semua query menggunakan parameter binding atau query builder.
- [ ] Logika bisnis kompleks diletakkan di `app/Services/`, bukan menumpuk di `app/Controllers/`.
- [ ] Model memiliki `$table`, `$primaryKey`, dan `$allowedFields` yang didefinisikan secara eksplisit.
- [ ] Semua form input di-handle via array-based validation rules.
- [ ] Output HTML di-escape dengan `esc($var, $context)`.
- [ ] Password user di-hash menggunakan `password_hash()` dan diverifikasi via `password_verify()`.
- [ ] Menjalankan `./vendor/bin/php-cs-fixer fix` tanpa error linter.
- [ ] Menjalankan `./vendor/bin/phpunit` dan semua tes berhasil (Green / 100% pass).
