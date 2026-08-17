
---

# 8. `docs/sync-contract.md`

**Celui-ci est le plus stratégique.**

```md
# Synchronization Contract

## 1. Purpose

This document defines the contract between local store applications
and the central server.

The contract must be defined before the central API is implemented.

Changes to this document may have an impact on:

- local database;
- local sync engine;
- central API;
- central database;
- dashboard;
- tests.

---

# 2. Synchronization Principles

The synchronization system MUST:

- support offline operation;
- be asynchronous;
- tolerate network failures;
- support retries;
- be idempotent;
- prevent duplicate business operations;
- provide synchronization status;
- provide sufficient logs for troubleshooting.

---

# 3. Event Identity

Every synchronization event must have a globally unique `event_id`.

Example:

```text
event_id = evt_01KXXXXXXXXXXXX