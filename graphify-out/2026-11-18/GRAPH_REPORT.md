# Graph Report - cater_web_ci4  (2026-11-17)

## Corpus Check
- 177 files · ~42,069 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 24 file(s) not represented in the graph (top: (none) 19, .jsonl 1, .cache 1)

## Summary
- 900 nodes · 1347 edges · 85 communities (25 shown, 60 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 97 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `be92a0a4`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- web
- web
- app
- app
- Filters.php
- ExampleDatabaseTest.php
- Cache.php
- BaseController
- CODING RULES & ARCHITECTURAL GUIDELINES
- composer.json
- Services
- BacaMeterModel
- CodeIgniter\Config\BaseConfig
- CodeIgniter\HTTP\ResponseInterface
- Project Rules for AI Agents: cater_web_ci4 (Antigravity 2.0 Optimized)
- FlattenByKondisi
- BacaMeterQueryService
- Project Instructions for AI Agents
- CodeIgniter\Test\CIUnitTestCase
- Running Application Tests
- CONTEXT.md — cater_web_ci4
- Toolbar.php
- Beads - AI-Native Issue Tracking
- Cater Web (CodeIgniter 4 - `cater_web_ci4`)
- Exceptions.php
- Format.php
- Kint.php
- Config/Validation.php
- debug.js
- ExampleMigration
- SelisihFotoService
- Mimes
- Cookie.php
- Verif0MeterReconciliationService
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
1. `BaseController` - 68 edges
2. `BacaMeterQueryService` - 63 edges
3. `BacaMeterModel` - 31 edges
4. `Verif0MeterReconciliationService` - 26 edges
5. `FlattenByKondisi` - 21 edges
6. `Session` - 20 edges
7. `ImageToString` - 18 edges
8. `CabangModel` - 16 edges
9. `HasilBacaSummaryService` - 14 edges
10. `SelisihFotoService` - 14 edges

## Surprising Connections (you probably didn't know these)
- `6.1 Manajemen Foto Meter Air (`CekFoto`, `FotoMeter`)` --references--> `FotoMeter`  [INFERRED]
  CODING_RULES.md → app/Controllers/Cetak/FotoMeter.php
- `3.2 Service Layer (`app/Services/`)` --references--> `BacaMeterModel`  [INFERRED]
  CODING_RULES.md → app/Models/BacaMeterModel.php
- `3.2 Service Layer (`app/Services/`)` --references--> `CustModel`  [INFERRED]
  CODING_RULES.md → app/Models/Sikompak/CustModel.php
- `3.2 Service Layer (`app/Services/`)` --references--> `Services`  [INFERRED]
  CODING_RULES.md → app/Config/Services.php
- `{closure#1}()` --calls--> `str_contains()`  [INFERRED]
  tests/app/Services/Verif0MeterReconciliationServiceTest.php → preload.php

## Import Cycles
- None detected.

## Communities (85 total, 60 thin omitted)

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

### Community 6 - "ExampleDatabaseTest.php"
Cohesion: 0.25
Nodes (3): ExampleDatabaseTest, ExampleSeeder, ExampleModel

### Community 20 - "BaseController"
Cohesion: 0.05
Nodes (23): Session, User, Cabang, Pegawai, BaseController, CekFoto, Selisih, Verif (+15 more)

### Community 32 - "CODING RULES & ARCHITECTURAL GUIDELINES"
Cohesion: 0.07
Nodes (27): 1. Prinsip Utama & Filosofi Desain, 2.1 Deklarasi Strict Types Wajib, 2.2 Explicit Property & Return Type Hints, 2.3 Memanfaatkan Fitur Modern PHP 8.1+, 2. Standar Bahasa PHP 8.1+ & PSR-12, 4. Validasi Form & Input Handling, 5.1 Proteksi CSRF (Cross-Site Request Forgery), 5.2 Pencegahan XSS (Cross-Site Scripting) (+19 more)

### Community 49 - "composer.json"
Cohesion: 0.06
Nodes (33): autoload, autoload-dev, psr-4, exclude-from-classmap, psr-4, config, optimize-autoloader, platform (+25 more)

### Community 54 - "Services"
Cohesion: 0.10
Nodes (12): App, Services, 3.1 Controller Rules (`app/Controllers/`), 3.2 Service Layer (`app/Services/`), 3.3 Model Rules (`app/Models/`), 3.4 Entity Rules (`app/Entities/`), 3. Arsitektur Berlapis: Controller - Service - Model - Entity, Contoh Controller yang Sesuai Standar: (+4 more)

### Community 55 - "BacaMeterModel"
Cohesion: 0.06
Nodes (7): Target, {closure#1}(), FotoMeter, ImageToString, BacaMeterModel, ImageToStringTest, ImageToStringTest

### Community 62 - "CodeIgniter\Config\BaseConfig"
Cohesion: 0.11
Nodes (13): ContentSecurityPolicy, Cors, CURLRequest, Email, Encryption, Feature, Generators, Honeypot (+5 more)

### Community 82 - "CodeIgniter\HTTP\ResponseInterface"
Cohesion: 0.08
Nodes (8): HasilBaca, GrafikVerifikasi0Pelanggan, Auth, AuthFilter, AuthModel, CustModel, {closure#1}(), HasilBacaSummaryService

### Community 91 - "Project Rules for AI Agents: cater_web_ci4 (Antigravity 2.0 Optimized)"
Cohesion: 0.06
Nodes (28): Agent Instructions — CodeIgniter 4 Project (cater_web_ci4), Always Do, Beads Issue Tracker, CLI, Coding Standards, GitNexus — Code Intelligence, Never Do, Project Context (+20 more)

### Community 97 - "FlattenByKondisi"
Cohesion: 0.11
Nodes (8): get_start_end_date_from_periode(), get_start_end_date_from_year_month(), RekapKondisiBaca0, RekapKondisiBaca, FlattenByKondisi, KondisiToExcel, DateTest, FlattenByKondisiTest

### Community 101 - "BacaMeterQueryService"
Cohesion: 0.07
Nodes (10): CekFoto, HasilBaca0, HasilBaca, Kondisi, KondisiBaca0, DetailHasilBaca0, Verif0MeterGrafikService, Verif0MeterService (+2 more)

### Community 137 - "Project Instructions for AI Agents"
Cohesion: 0.14
Nodes (13): Always Do, Architecture Overview, Beads Issue Tracker, Build & Test, CLI, Conventions & Patterns, GitNexus — Code Intelligence, Never Do (+5 more)

### Community 161 - "CodeIgniter\Test\CIUnitTestCase"
Cohesion: 0.08
Nodes (9): DetailHasilBaca, getMonthName(), listBulan(), HasilBacaToExcel, HasilBacaSummaryServiceTest, ExampleSessionTest, BulanHelperTest, HasilBacaToExcelTest (+1 more)

### Community 179 - "Running Application Tests"
Cohesion: 0.18
Nodes (10): Creating Tests, Database Tests, Generating Code Coverage, PHPUnit XML Configuration, Requirements, Resources, Running Application Tests, Running the tests (+2 more)

### Community 180 - "CONTEXT.md — cater_web_ci4"
Cohesion: 0.29
Nodes (6): Architectural Decision Records (ADRs), CONTEXT.md — cater_web_ci4, Directory Structure & Key Modules, Key Development & Testing Commands, Overview, Tech Stack

### Community 203 - "Beads - AI-Native Issue Tracking"
Cohesion: 0.22
Nodes (8): Beads - AI-Native Issue Tracking, Essential Commands, Get Started with Beads, Learn More, Quick Start, What is Beads?, Why Beads?, Working with Issues

### Community 241 - "Cater Web (CodeIgniter 4 - `cater_web_ci4`)"
Cohesion: 0.22
Nodes (8): 1. Local Development (Koding Langsung di Host), 2. Docker Deployment (Containerized), 🐳 Arsitektur Docker, Cater Web (CodeIgniter 4 - `cater_web_ci4`), 🚀 Dua Mode Lingkungan Kerja, 📊 Fitur Snapshot Rekonsiliasi Verifikasi 0 Meter, 🛠️ Panduan Perintah `Makefile`, 📋 Server Requirements (Local Mode)

### Community 264 - "debug.js"
Cohesion: 0.53
Nodes (4): getFirstChildWithTagName(), getHash(), init(), showTab()

### Community 300 - "SelisihFotoService"
Cohesion: 0.12
Nodes (5): View, Selisih, SelisihFoto, SelisihFotoService, SelisihFotoServiceTest

### Community 304 - "Verif0MeterReconciliationService"
Cohesion: 0.05
Nodes (11): GenerateVerif0Snapshot, Database, Paths, GrafikVerifikasi0, Verif0MeterPelangganService, Verif0MeterReconciliationService, preload, str_contains() (+3 more)

### Community 755 - "package.json"
Cohesion: 0.67
Nodes (3): dependencies, puppeteer@^25.13.0, puppeteer

## Knowledge Gaps
- **60 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `BaseController` connect `BaseController` to `CodeIgniter\Test\CIUnitTestCase`, `FlattenByKondisi`, `BacaMeterQueryService`, `SelisihFotoService`, `Verif0MeterReconciliationService`, `CodeIgniter\HTTP\ResponseInterface`, `BacaMeterModel`?**
  _High betweenness centrality (0.127) - this node is a cross-community bridge._
- **Should `web` be split into smaller, more focused modules?**
  _Cohesion score 0.06881720430107527 - nodes in this community are weakly interconnected._
- **Why does `BacaMeterModel` connect `BacaMeterModel` to `CodeIgniter\HTTP\ResponseInterface`, `BaseController`, `BacaMeterQueryService`, `Services`?**
  _High betweenness centrality (0.073) - this node is a cross-community bridge._
- **Should `web` be split into smaller, more focused modules?**
  _Cohesion score 0.06881720430107527 - nodes in this community are weakly interconnected._
- **Why does `CODING RULES & ARCHITECTURAL GUIDELINES` connect `CODING RULES & ARCHITECTURAL GUIDELINES` to `Project Rules for AI Agents: cater_web_ci4 (Antigravity 2.0 Optimized)`, `Services`?**
  _High betweenness centrality (0.073) - this node is a cross-community bridge._
- **Should `app` be split into smaller, more focused modules?**
  _Cohesion score 0.07126436781609195 - nodes in this community are weakly interconnected._
- **Should `app` be split into smaller, more focused modules?**
  _Cohesion score 0.07126436781609195 - nodes in this community are weakly interconnected._