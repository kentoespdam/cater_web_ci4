# CONTEXT.md — cater_web_ci4

## Overview
`cater_web_ci4` is a web-based water meter reading management system (**Catat Meter PDAM**) built with **CodeIgniter 4** (PHP 8.1+). It handles field meter reading data collection, photo verification, variance analysis (`Selisih`), condition reporting (`Kondisi`), master data management (branches, officers, units, customers), and API integration for mobile readers.

---

## Tech Stack
- **Framework**: CodeIgniter 4 (`^4.0`)
- **Language**: PHP 8.1+ (Platform configured for PHP 8.3)
- **Export Library**: `openspout/openspout` (`^5.3`) for high-performance Excel/CSV generation
- **Testing**: PHPUnit (`^10.5`)
- **Linting / Formatting**: PHP CS Fixer (`^3.54`)
- **Containerization**: Docker (Alpine PHP-FPM + Nginx) with 3-Layer Volume Architecture
- **Issue Tracking**: Beads (`bd`)

---

## Directory Structure & Key Modules
```
app/
├── Controllers/
│   ├── Api/               # REST API endpoints for mobile/Sikompak apps (HasilBaca, CekFoto, Sikompak/*)
│   ├── Cetak/             # Printable reports (DetailHasilBaca, FotoMeter, RekapKondisi, SelisihFoto)
│   ├── HasilBaca/         # Meter reading workflows (CekFoto, Selisih, Verif)
│   ├── Laporan/           # Reporting modules (HasilBaca, Kondisi, Target)
│   ├── Master/            # Master data management (User, TransferKampung)
│   └── Auth.php           # Authentication controller
├── Models/
│   ├── AuthModel.php      # User authentication model
│   ├── BacaMeterModel.php # Meter reading data model
│   ├── KondisiModel.php   # Meter condition lookup/model
│   └── Sikompak/          # Sikompak master models (Cabang, Cust, Pegawai, Unit)
├── Views/                 # HTML templates (AdminLTE based UI)
└── Database/              # Database configurations & migrations
docs/
└── adr/                   # Architecture Decision Records
```

---

## Key Development & Testing Commands
- **Local Development**: `php spark serve` (or `make dev-serve` on port 8080)
- **Docker Up**: `make up` (runs containers on port 84 via `.env.docker`)
- **Unit Testing**: `./vendor/bin/phpunit` (or `make test`)
- **Linting Check**: `vendor/bin/php-cs-fixer fix --dry-run` (or `make lint`)
- **Routes Inspection**: `php spark routes` (or `make routes`)

---

## Architectural Decision Records (ADRs)
See [docs/adr/](docs/adr/) for detailed architectural decisions:
1. [ADR-0001: CodeIgniter 4 Framework Choice](docs/adr/0001-codeigniter-4-framework-choice.md) - Adoption of CodeIgniter 4 for robust MVC structure and native CLI tools (`spark`).
2. [ADR-0002: Docker Containerized Deployment](docs/adr/0002-docker-containerized-deployment.md) - Multi-stage Docker build and 3-layer volume architecture with zero auto-migration for production safety.
3. [ADR-0003: OpenSpout for Excel Export](docs/adr/0003-openspout-for-excel-export.md) - Use of OpenSpout over older libraries for efficient memory streaming in Excel exports.
4. [ADR-0004: Separate Endpoints for Laporan HasilBaca and HasilBaca0](docs/adr/0004-separate-endpoints-for-hasilbaca-and-hasilbaca0.md) - Intentional architectural separation of regular and zero-consumption meter reading reports.
