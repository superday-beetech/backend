# Pharmacy Upgrade — Foundation

This directory is an isolated workspace for non-operational CI4 foundation work.

## Repository baseline

- Framework: CodeIgniter 4.7.5.
- Authentication and authorization: CodeIgniter Shield in the main application.
- Existing module namespace: `Modules\\Pharmacy`.
- Existing module model base: `Modules\\Pharmacy\\Models\\PharmacyModel`.
- The existing Pharmacy module has its own dynamic route entry point in `modules/Pharmacy/Config/Routes.php`.
- The existing Pharmacy code and database are treated as read-only references for this upgrade workspace.

## Scope of this workspace

Allowed foundation topics:

- Module organization and namespace conventions.
- CI4 bootstrap and configuration patterns.
- Shared authentication integration and authorization boundaries.
- Generic API request/response conventions.
- Generic validation and exception handling.
- Logging, environment configuration, and application-level diagnostics.
- Automated tests for the foundation itself.

This workspace does not implement or modify medication procurement, inventory, sales, dispensing, delivery, or distribution workflows.

## Change policy

1. Keep all work in `modules/Pharmacy-Upgrade`.
2. Do not modify files under `modules/Pharmacy`.
3. Do not add or run database migrations from this workspace.
4. Do not assume configuration paths or APIs; verify them against the repository before implementation.
5. Keep authentication and authorization integrated with the existing main application rather than creating a second identity store.
