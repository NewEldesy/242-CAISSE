# AGENTS.md

## Project State

Phase 3–4: local POS architecture and implementation. No central API, dashboard, or bidirectional sync yet unless explicitly requested.
Local: SQLite. Central (future): Laravel.

---

## Hard Constraints

- **Offline-first**: every local business operation (sale, payment, stock) must work without Internet. Sync is async and must never block local operations.
- **Sync contract first**: `docs/sync-contract.md` is the architectural backbone. Any change to a synchronizable entity (products, stock, sales, sale_items, payments, customers, users, stores, devices, sync_queue, sync_logs) must respect this contract.
- **Globally unique identifiers**: use `Uuid::generate()` (UUID v4) for every entity ID. Do not introduce sequential local IDs for any synchronized entity.
- **Database changes require migration + sync impact analysis**: schema changes go through `packages/infrastructure/src/Migration/Migrations.php` and must document affected models, relationships, and synchronization impact.

---

## Monorepo Layout

```
packages/
  domain/          — Entities, Value Objects, Business Rules (no framework deps)
  infrastructure/  — SQLite Connection, Repositories, Migrations (depends on domain)
  cli/             — POS CLI entry point, Commands (depends on domain + infrastructure)
  sync/            — Sync Queue, Worker, Strategy (depends on domain + infrastructure)
tests/
  Unit/            — Pure domain tests
  Integration/     — Tests with temp SQLite databases and real migrations
```

Root `composer.json` is the application manifest. Packages are linked via path repositories.

---

## Commands

```bash
composer install           # install root + package deps
composer test              # run PHPUnit (vendor/bin/phpunit)
vendor/bin/phpunit         # same, direct
php scripts/migrate.php [database/caisse.db]   # run migrations (idempotent, wrapped in transaction)
php scripts/status.php                         # list tables + row counts for default DB
php packages/cli/bin/pos init                  # initialize a new store DB
php packages/cli/bin/pos <command>             # run POS commands (see below)
```

Composer scripts: `composer test`, `composer migrate`, `composer pos`, `composer init-store`

**CLI commands**: `init`, `store:create`, `user:create`, `device:create`, `device:list`, `product:create`, `product:list`, `sale:create`, `sale:list`, `stock:list`, `session:open`, `session:close`

The `pos` binary auto-detects whether the first argument is a command or a DB path. If it is not a known command, it treats it as `database/caisse.db` and shifts the command to the next argument: `php packages/cli/bin/pos database/moncompte.db <command>`.

---

## Tests

```bash
vendor/bin/phpunit                                # all tests
vendor/bin/phpunit tests/Unit/SaleTest.php        # single test file
vendor/bin/phpunit --filter testCompletesSale     # single test method
```

Integration tests (`tests/Integration`) create a temp SQLite file in `sys_get_temp_dir()`, run the real migrations in `setUp()`, and delete it in `tearDown()`.

---

## Architecture Notes

- **Database Connection**: `Caisse\Infrastructure\Database\Connection::getInstance($path)` is a singleton with `PRAGMA foreign_keys = ON` and `PRAGMA journal_mode = WAL`. Tests must instantiate with their own temp path to avoid cross-test contamination.
- **Migrations**: flat array of SQL strings in `Migrations.php`, tracked by a `migrations` table using full SQL hash. No versioned migration files.
- **Entity reconstitution**: when hydrating entities from the database, use `Entity::reconstitute(...)` (reflection-based) to bypass constructor immutability and restore `id`, `createdAt`, `updatedAt`.
- **Business invariants** (from `docs/domain.md`):
  - Sale item prices are immutable — changing a product price must not affect historical sales.
  - A sale can only be completed if total payments ≥ total amount.
  - Sales, payments, and stock updates must be atomic and traceable.

---

## Key Docs (read before non-trivial changes)

- `docs/sync-contract.md` — what is exchanged
- `docs/sync-strategy.md` — how it is synchronized
- `docs/domain.md` — business entities and invariants
- `docs/database.md` — entity list and local schema direction
