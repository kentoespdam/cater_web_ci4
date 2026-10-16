# ADR-0001: CodeIgniter 4 Framework Choice

## Status
Accepted

## Context
The application (`cater_web_ci4`) is a water meter reading management system (Catat Meter PDAM) used by field officers and administrators. It requires a robust PHP web framework that provides fast request handling, an intuitive MVC structure, built-in security features (CSRF, XSS protection), and strong CLI tooling (`spark`) for maintenance and batch tasks.

## Decision
We adopted **CodeIgniter 4** as the primary web framework for `cater_web_ci4`.

## Consequences
- **Positive**: Lightweight footprint, rapid development cycle, excellent documentation, native CLI support (`php spark`), and zero complex configuration overhead compared to heavier enterprise frameworks.
- **Negative / Trade-offs**: Smaller ecosystem of plug-and-play packages compared to Laravel, requiring custom implementation for some advanced enterprise modules.

## Alternatives Considered
- **Laravel**: Considered due to its massive ecosystem, but dismissed as overly heavy and resource-intensive for the specific operational scope of this meter-reading application.
- **Vanilla PHP / Custom MVC**: Considered for ultimate control, but rejected due to the high maintenance burden of writing secure routing, database abstraction, and input validation layers from scratch.
