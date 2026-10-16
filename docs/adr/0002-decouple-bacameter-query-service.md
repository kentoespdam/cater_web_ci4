# ADR-0002: Decouple Reporting Query Layer & Extract HasilBacaSummaryService

## Status
Accepted

## Context
In `cater_web_ci4`, `BacaMeterModel` accumulated over 411 lines of mixed ORM persistence, complex SQL joins, reporting aggregations, and generators. Similarly, `Api\HasilBaca` controller contained business aggregation rules and caching logic. This created shallow modules and tightly coupled HTTP endpoints to database query implementations.

## Decision
1. Introduce `App\Services\BacaMeterQueryService` to encapsulate complex reporting queries and data aggregations previously housed in `BacaMeterModel`.
2. Extract `App\Services\HasilBacaSummaryService` to handle result formatting, progress calculations, and cache management for meter reading summaries.
3. Refactor `Api\HasilBaca` controller to be a thin HTTP controller delegating entirely to `HasilBacaSummaryService`.

## Consequences
- **Improved Testability**: Domain services and summary aggregation logic can now be unit-tested independently of HTTP requests and raw database calls.
- **Better Separation of Concerns**: Controllers remain thin, models focus on data access, and services handle business logic and orchestration.
- **Maintained Backward Compatibility**: Existing callers referencing model reporting methods continue to function cleanly.
