# CLAUDE.md

## Project

Multi-Store Management System.

This project is an offline-first multi-store management application.

Each store runs a local POS application backed by SQLite.

A central Laravel server will later receive synchronized data and provide
a web dashboard.

---

# Current Development Strategy

The project is intentionally developed in stages.

Current priority:

1. Local POS
2. Offline behavior
3. Local synchronization engine
4. Central synchronization API
5. Central dashboard
6. Bidirectional synchronization

Do not jump directly to later phases unless explicitly requested.

---

# Current Phase

Phase: Initial architecture and local POS development.

The synchronization contract must already be respected even though the
central API does not yet exist.

---

# Important Documentation

Before making architectural or business-related changes, read:

- `docs/product.md`
- `docs/architecture.md`
- `docs/domain.md`
- `docs/database.md`
- `docs/sync-contract.md`
- `docs/sync-strategy.md`

For roadmap and task scope:

- `docs/roadmap.md`

---

# Development Behavior

When receiving a task:

## Step 1 — Understand

Inspect:

- existing code;
- relevant documentation;
- database schema;
- tests;
- related services.

## Step 2 — Plan

For non-trivial tasks, explain:

- files to modify;
- files to create;
- database impact;
- synchronization impact;
- tests required.

## Step 3 — Implement

Implement only the requested functionality.

## Step 4 — Test

Run the smallest relevant test set first.

Then run broader tests when appropriate.

## Step 5 — Review

Check:

- business logic;
- security;
- data integrity;
- offline behavior;
- synchronization compatibility;
- regressions.

---

# Special Rule: Synchronization

Before modifying a synchronizable entity, inspect:

`docs/sync-contract.md`

If the modification changes:

- entity structure;
- identifier;
- event;
- payload;
- operation;
- synchronization state;

the synchronization documentation and tests must be considered.

---

# Special Rule: Business-Critical Operations

Sales, payments and stock operations are business-critical.

Do not optimize for code brevity at the expense of:

- correctness;
- atomicity;
- traceability;
- recoverability.

---

# Do Not

Do not:

- invent undocumented business rules;
- create unnecessary abstractions;
- rewrite large sections without justification;
- introduce dependencies without evaluating them;
- couple local business logic directly to the network;
- assume Internet availability;
- assume requests are delivered exactly once;
- silently change synchronization behavior.

---

# Expected Response After Implementation

After completing a task, report:

1. Summary
2. Files created
3. Files modified
4. Database changes
5. Synchronization impact
6. Tests executed
7. Remaining concerns

If tests cannot be executed, explain why.