# ADR-0002: Docker Containerized Deployment & 3-Layer Volume Architecture

## Status
Accepted

## Context
Deploying `cater_web_ci4` across staging and production environments requires consistent PHP 8.1+ runtime environments (Alpine PHP-FPM + Nginx) while preventing accidental database corruptions or unintended automatic migrations during container startups. Furthermore, storage persistence for user-uploaded meter photos (`fotowm`) and application data (`writable`) must be strictly managed.

## Decision
We implemented a multi-stage Docker build combined with a **3-Layer Volume Architecture** (`app_code`, `writable_data`, `fotowm`) and explicitly disabled auto-migrations during container startup, enforcing manual migration execution via `php spark`.

## Consequences
- **Positive**: Highly reproducible production builds, slim runtime images without development dependencies or Composer build tools, persistent isolation of user uploads and logs, and complete safety against accidental database mutations on startup.
- **Negative / Trade-offs**: Requires container management expertise (Docker Compose / Makefile) for local debugging and deployment workflows.

## Alternatives Considered
- **Direct Host Deployment (Traditional LEMP)**: Considered for simplicity, but rejected due to environment inconsistencies across different staging and production servers.
- **Single-Stage Heavy Docker Image**: Considered, but rejected because it would include unnecessary build tools and Composer caches, increasing image size and security attack surface.
