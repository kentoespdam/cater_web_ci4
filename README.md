# Cater Web (CodeIgniter 4 - `cater_web_ci4`)

Aplikasi web manajemen katering berbasis framework **CodeIgniter 4** (PHP 8.2+). Repositori ini mendukung dua mode lingkungan kerja yang fleksibel: pengembangan lokal langsung di host atau containerized deployment menggunakan Docker.

---

## 🚀 Dua Mode Lingkungan Kerja

### 1. Local Development (Koding Langsung di Host)
Digunakan ketika Anda ingin melakukan koding secara langsung di mesin host tanpa menggunakan container Docker.
* **Konfigurasi Environment**: Menggunakan file `.env` lokal host (salin dari `env`).
* **Perintah Menjalankan Server**:
  ```bash
  make dev-serve
  # atau secara langsung:
  php spark serve
  ```
* **Akses**: `http://localhost:8080`
* **Database**: Terhubung ke database lokal (MySQL/MariaDB) sesuai konfigurasi pada file `.env`.

### 2. Docker Deployment (Containerized)
Digunakan untuk environment production, staging, atau pengujian yang terisolasi dan konsisten menggunakan Docker & Nginx + PHP-FPM.
* **Konfigurasi Environment**: Menggunakan file `.env.docker` terisolasi.
* **Perintah Menjalankan**:
  ```bash
  make up
  # atau secara manual:
  docker compose --env-file .env.docker up -d
  ```
* **Akses**: `http://localhost:84` (Port dapat disesuaikan melalui `APP_PORT` di `.env.docker`).
* **Database**: Terhubung ke external/host database (menggunakan `host.docker.internal` atau IP server database eksternal).

---

## 🐳 Arsitektur Docker

Arsitektur Docker pada proyek ini dirancang dengan prinsip keamanan, performa, dan kemudahan deployment:

1. **Multi-Stage Build**
   - **Stage 1 (`vendor-builder`)**: Menggunakan image resmi `composer:2` untuk menginstal dependensi produksi (`composer install --no-dev`) tanpa menyertakan build tools di image akhir.
   - **Stage 2 (`php:8.2-fpm-alpine`)**: Runtime production yang ramping, ringan, dan aman, bersih dari file cache composer yang tidak perlu.
2. **Isolated Container**
   - Source code disalin ke dalam image saat proses *build*. Komputer host **tidak perlu** menginstal PHP atau Composer untuk menjalankan Docker deployment.
3. **3-Layer Volume Architecture**
   - **`app_code`**: Shared named volume antara layanan PHP-FPM (`rw`) dan Nginx (`ro`) untuk memastikan sinkronisasi kode secara *real-time* dan menjadikan kode sebagai *single source of truth*.
   - **`writable_data`**: Dedicated persistent volume (`/var/www/html/writable`) untuk menyimpan cache, logs, dan sessions secara aman di luar container lifespan.
   - **`fotowm`**: Host bind mount (`./fotowm`) untuk penyimpanan persisten foto watermark/galeri.
4. **Database Safety Guarantee (Zero Auto-Migration)**
   - Tidak ada migrasi database otomatis saat container startup. Seluruh eksekusi migrasi atau seeder dilakukan secara manual melalui perintah `spark`, menjadikannya sangat aman untuk production.

---

## 🛠️ Panduan Perintah `Makefile`

Seluruh operasional Docker, CodeIgniter Spark, Composer, testing, dan linting dikelola melalui `Makefile`. 

| Perintah | Deskripsi |
|---|---|
| `make help` | Menampilkan daftar lengkap perintah Makefile yang tersedia |
| `make build` | Membangun ulang (build) image container Docker |
| `make up` | Menjalankan container Docker di background (port 84) |
| `make down` | Menghentikan dan menghapus container Docker |
| `make restart` | Merestart container Docker |
| `make rebuild` | Menghentikan container, membersihkan volume kode, dan build ulang dari awal |
| `make ps` | Melihat status container yang sedang berjalan |
| `make logs` | Memantau live logs dari semua service (Nginx & PHP-FPM) |
| `make logs-app` | Memantau live logs dari service PHP-FPM (`app`) |
| `make logs-web` | Memantau live logs dari service Nginx (`web`) |
| `make bash` | Masuk ke shell container PHP-FPM (`app`) |
| `make bash-web` | Masuk ke shell container Nginx (`web`) |
| `make routes` | Menampilkan daftar route CodeIgniter 4 (`php spark routes`) |
| `make cache-clear` | Membersihkan cache aplikasi (`php spark cache:clear`) |
| `make spark CMD="..."`| Menjalankan perintah custom CodeIgniter Spark |
| `make composer-install`| Menjalankan `composer install` di dalam container |
| `make composer-update`| Menjalankan `composer update` di dalam container |
| `make test` | Menjalankan unit testing PHPUnit di dalam container |
| `make lint` | Menjalankan linting PHP CS Fixer (dry-run) di dalam container |
| `make dev-serve` | Menjalankan development server lokal di host (`http://localhost:8080`) |

---

## 📋 Server Requirements (Local Mode)

Jika menjalankan mode lokal tanpa Docker, pastikan PHP 8.1+ terinstal dengan ekstensi berikut:
- `intl`
- `mbstring`
- `json`
- `mysqlnd`
- `libcurl`
- `gd`, `exif`, `zip`, `opcache`, `bcmath`
