# ADR-0004: Separate Endpoints for Laporan HasilBaca and HasilBaca0

## Status
Accepted (October 2026)

## Context
In the `cater_web_ci4` water meter reading management system, the reporting modules include two distinct report types: `HasilBaca` (`App\Controllers\Laporan\HasilBaca`) and `HasilBaca0` (`App\Controllers\Laporan\HasilBaca0`). 

- **HasilBaca**: Generates comprehensive meter reading reports containing all recorded water consumption data across regular readings.
- **HasilBaca0**: Specifically isolates and filters water consumption records with zero usage (`pemakaian = 0` / zero cubic meters), which is a crucial operational metric for detecting water meter blockages, unread meters, or zero-consumption accounts.

Superficially, both controllers share similar boilerplate structures (fetching branches/conditions, rendering reporting views). Consequently, future developers or code quality tools might view this duplication as a code smell or a violation of the DRY (Don't Repeat Yourself) principle, proposing to merge them into a single parameterized controller (e.g., passing a query parameter like `?zero=true`).

However, architectural requirements dictate that these endpoints remain strictly separated.

## Decision
We formally decide to **maintain separate controllers and endpoints** (`HasilBaca` and `HasilBaca0`) as an **intentional architectural decision**, rather than refactoring them into a unified parameterized controller.

Each report maintains its own dedicated controller class, route mapping, and presentation flow, ensuring clear operational boundaries.

## Consequences
- **Positive**: 
  - Clear URL and endpoint isolation for user navigation, bookmarking, and role-based access control (RBAC).
  - Prevents query logic regression or accidental mixing between general meter reading reports and zero-consumption audit reports.
  - Keeps individual controllers clean and focused on their specific business logic without complex conditional branching.
- **Negative / Trade-offs**: 
  - Minor code duplication across controllers and view templates that requires concurrent maintenance if shared report layout structures change.

## Alternatives Considered
- **Single Parameterized Controller (Query Parameters / Filters)**: Considered merging both reports into a single controller with a query parameter (e.g., `?filter=zero`). Rejected because operational workflows, user bookmarking, menu structures, and distinct authorization requirements strongly favor explicit, decoupled URLs.
- **Base Controller / Trait Extraction**: Considered extracting shared lookup queries (such as branch retrieval) into a shared trait or base class. This remains an acceptable minor refactoring option if code divergence grows, but the endpoint routing and controller classes must remain decoupled.
