# Graph Report - cater_web_ci4  (2026-11-12)

## Corpus Check
- 167 files · ~38,824 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 24 file(s) not represented in the graph (top: (none) 19, .jsonl 1, .cache 1)

## Summary
- 847 nodes · 1239 edges · 85 communities (26 shown, 59 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 82 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5b7254f8`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- web
- web
- app
- app
- CURLRequest.php
- BaseController
- CODING RULES & ARCHITECTURAL GUIDELINES
- composer.json
- Services
- ImageToString
- CodeIgniter\Config\BaseConfig
- CodeIgniter\HTTP\ResponseInterface
- Project Rules for AI Agents: cater_web_ci4 (Antigravity 2.0 Optimized)
- FlattenByKondisi
- BacaMeterQueryService
- Project Instructions for AI Agents
- Target
- CodeIgniter\Test\CIUnitTestCase
- Running Application Tests
- CONTEXT.md — cater_web_ci4
- BacaMeterModel
- Toolbar.php
- Beads - AI-Native Issue Tracking
- preload
- Cater Web (CodeIgniter 4 - `cater_web_ci4`)
- Exceptions.php
- PegawaiModel
- Format.php
- Kint.php
- Config/Validation.php
- debug.js
- ExampleMigration
- SelisihFotoService
- Mimes
- Cookie.php
- Autoload.php
- ForeignCharacters.php
- Modules.php
- Publisher.php
- Routing.php
- DocTypes.php
- Optimize.php
- kondisi.php
- kondisi_pakai_0.php
- transfer_kampung.php
- Images.php
- package.json

## God Nodes (most connected - your core abstractions)
1. `BacaMeterQueryService` - 63 edges
2. `BaseController` - 62 edges
3. `BacaMeterModel` - 31 edges
4. `FlattenByKondisi` - 21 edges
5. `Session` - 18 edges
6. `ImageToString` - 18 edges
7. `CabangModel` - 16 edges
8. `HasilBacaSummaryService` - 14 edges
9. `SelisihFotoService` - 14 edges
10. `KondisiModel` - 12 edges

## Surprising Connections (you probably didn't know these)
- `6.1 Manajemen Foto Meter Air (`CekFoto`, `FotoMeter`)` --references--> `FotoMeter`  [INFERRED]
  CODING_RULES.md → app/Controllers/Cetak/FotoMeter.php
- `3.2 Service Layer (`app/Services/`)` --references--> `BacaMeterModel`  [INFERRED]
  CODING_RULES.md → app/Models/BacaMeterModel.php
- `3.2 Service Layer (`app/Services/`)` --references--> `CustModel`  [INFERRED]
  CODING_RULES.md → app/Models/Sikompak/CustModel.php
- `3.2 Service Layer (`app/Services/`)` --references--> `Services`  [INFERRED]
  CODING_RULES.md → app/Config/Services.php
- `ConfigReader` --inherits--> `App`  [EXTRACTED]
  tests/_support/Libraries/ConfigReader.php → app/Config/App.php

## Import Cycles
- None detected.

## Communities (85 total, 59 thin omitted)

### Community 0 - "web"
Cohesion: 0.07
Nodes (30): build, driver, container_name, extra_hosts, networks, restart, volumes, GID (+22 more)

### Community 1 - "web"
Cohesion: 0.07
Nodes (30): build, driver, container_name, extra_hosts, networks, restart, volumes, GID (+22 more)

### Community 2 - "app"
Cohesion: 0.07
Nodes (29): build, container_name, environment, extra_hosts, networks, restart, volumes, GID (+21 more)

### Community 3 - "app"
Cohesion: 0.07
Nodes (29): build, container_name, environment, extra_hosts, networks, restart, volumes, GID (+21 more)

### Community 20 - "BaseController"
Cohesion: 0.08
Nodes (16): Session, Cabang, BaseController, CekFoto, Selisih, Verif, Home, GrafikVerifikasi0 (+8 more)

### Community 32 - "CODING RULES & ARCHITECTURAL GUIDELINES"
Cohesion: 0.07
Nodes (28): FotoMeter, 1. Prinsip Utama & Filosofi Desain, 2.1 Deklarasi Strict Types Wajib, 2.2 Explicit Property & Return Type Hints, 2.3 Memanfaatkan Fitur Modern PHP 8.1+, 2. Standar Bahasa PHP 8.1+ & PSR-12, 4. Validasi Form & Input Handling, 5.1 Proteksi CSRF (Cross-Site Request Forgery) (+20 more)

### Community 49 - "composer.json"
Cohesion: 0.06
Nodes (33): autoload, autoload-dev, psr-4, exclude-from-classmap, psr-4, config, optimize-autoloader, platform (+25 more)

### Community 54 - "Services"
Cohesion: 0.10
Nodes (12): App, Services, 3.1 Controller Rules (`app/Controllers/`), 3.2 Service Layer (`app/Services/`), 3.3 Model Rules (`app/Models/`), 3.4 Entity Rules (`app/Entities/`), 3. Arsitektur Berlapis: Controller - Service - Model - Entity, Contoh Controller yang Sesuai Standar: (+4 more)

### Community 55 - "ImageToString"
Cohesion: 0.09
Nodes (9): {closure#1}(), getMonthName(), listBulan(), ImageToString, ImageToStringTest, HasilBacaSummaryServiceTest, ExampleSessionTest, BulanHelperTest (+1 more)

### Community 62 - "CodeIgniter\Config\BaseConfig"
Cohesion: 0.11
Nodes (12): ContentSecurityPolicy, Cors, Email, Encryption, Feature, Generators, Honeypot, Logger (+4 more)

### Community 82 - "CodeIgniter\HTTP\ResponseInterface"
Cohesion: 0.09
Nodes (4): Filters, View, Auth, AuthFilter

### Community 91 - "Project Rules for AI Agents: cater_web_ci4 (Antigravity 2.0 Optimized)"
Cohesion: 0.06
Nodes (28): Agent Instructions — CodeIgniter 4 Project (cater_web_ci4), Always Do, Beads Issue Tracker, CLI, Coding Standards, GitNexus — Code Intelligence, Never Do, Project Context (+20 more)

### Community 97 - "FlattenByKondisi"
Cohesion: 0.09
Nodes (10): get_start_end_date_from_periode(), get_start_end_date_from_year_month(), KondisiBaca0, RekapKondisiBaca0, RekapKondisiBaca, FlattenByKondisi, KondisiToExcel, DateTest (+2 more)

### Community 101 - "BacaMeterQueryService"
Cohesion: 0.05
Nodes (10): CekFoto, GrafikVerifikasi0, HasilBaca0, HasilBaca, Kondisi, Verif0MeterGrafikService, Verif0MeterService, BacaMeterModel (+2 more)

### Community 137 - "Project Instructions for AI Agents"
Cohesion: 0.14
Nodes (13): Always Do, Architecture Overview, Beads Issue Tracker, Build & Test, CLI, Conventions & Patterns, GitNexus — Code Intelligence, Never Do (+5 more)

### Community 161 - "CodeIgniter\Test\CIUnitTestCase"
Cohesion: 0.12
Nodes (4): DetailHasilBaca0, DetailHasilBaca, HasilBacaToExcel, HasilBacaToExcelTest

### Community 179 - "Running Application Tests"
Cohesion: 0.18
Nodes (10): Creating Tests, Database Tests, Generating Code Coverage, PHPUnit XML Configuration, Requirements, Resources, Running Application Tests, Running the tests (+2 more)

### Community 180 - "CONTEXT.md — cater_web_ci4"
Cohesion: 0.29
Nodes (6): Architectural Decision Records (ADRs), CONTEXT.md — cater_web_ci4, Directory Structure & Key Modules, Key Development & Testing Commands, Overview, Tech Stack

### Community 201 - "BacaMeterModel"
Cohesion: 0.10
Nodes (5): Cache, HasilBaca, CustModel, {closure#1}(), HasilBacaSummaryService

### Community 203 - "Beads - AI-Native Issue Tracking"
Cohesion: 0.22
Nodes (8): Beads - AI-Native Issue Tracking, Essential Commands, Get Started with Beads, Learn More, Quick Start, What is Beads?, Why Beads?, Working with Issues

### Community 217 - "preload"
Cohesion: 0.32
Nodes (3): Paths, preload, str_contains()

### Community 241 - "Cater Web (CodeIgniter 4 - `cater_web_ci4`)"
Cohesion: 0.25
Nodes (7): 1. Local Development (Koding Langsung di Host), 2. Docker Deployment (Containerized), 🐳 Arsitektur Docker, Cater Web (CodeIgniter 4 - `cater_web_ci4`), 🚀 Dua Mode Lingkungan Kerja, 🛠️ Panduan Perintah `Makefile`, 📋 Server Requirements (Local Mode)

### Community 243 - "PegawaiModel"
Cohesion: 0.08
Nodes (9): User, Pegawai, TransferKampung, AuthModel, PegawaiModel, UserAndroidModel, ExampleDatabaseTest, ExampleSeeder (+1 more)

### Community 264 - "debug.js"
Cohesion: 0.53
Nodes (4): getFirstChildWithTagName(), getHash(), init(), showTab()

### Community 300 - "SelisihFotoService"
Cohesion: 0.12
Nodes (5): Database, Selisih, SelisihFoto, SelisihFotoService, SelisihFotoServiceTest

### Community 755 - "package.json"
Cohesion: 0.67
Nodes (3): dependencies, puppeteer@^25.13.0, puppeteer

## Knowledge Gaps
- **59 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `BaseController` connect `BaseController` to `CODING RULES & ARCHITECTURAL GUIDELINES`, `FlattenByKondisi`, `CodeIgniter\Test\CIUnitTestCase`, `BacaMeterQueryService`, `BacaMeterModel`, `SelisihFotoService`, `Target`, `PegawaiModel`, `CodeIgniter\HTTP\ResponseInterface`, `ImageToString`?**
  _High betweenness centrality (0.101) - this node is a cross-community bridge._
- **Should `web` be split into smaller, more focused modules?**
  _Cohesion score 0.06881720430107527 - nodes in this community are weakly interconnected._
- **Why does `BacaMeterModel` connect `BacaMeterQueryService` to `CODING RULES & ARCHITECTURAL GUIDELINES`, `BacaMeterModel`, `Target`, `PegawaiModel`, `Services`, `ImageToString`?**
  _High betweenness centrality (0.080) - this node is a cross-community bridge._
- **Should `web` be split into smaller, more focused modules?**
  _Cohesion score 0.06881720430107527 - nodes in this community are weakly interconnected._
- **Why does `CODING RULES & ARCHITECTURAL GUIDELINES` connect `CODING RULES & ARCHITECTURAL GUIDELINES` to `Project Rules for AI Agents: cater_web_ci4 (Antigravity 2.0 Optimized)`, `Services`?**
  _High betweenness centrality (0.073) - this node is a cross-community bridge._
- **Should `app` be split into smaller, more focused modules?**
  _Cohesion score 0.07126436781609195 - nodes in this community are weakly interconnected._
- **Should `app` be split into smaller, more focused modules?**
  _Cohesion score 0.07126436781609195 - nodes in this community are weakly interconnected._