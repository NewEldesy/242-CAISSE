
---

# 9. `docs/sync-strategy.md`

Ce fichier est différent du précédent.

**`sync-contract.md` dit CE QUI est échangé.**

**`sync-strategy.md` dit COMMENT on le synchronise.**

```md
# Synchronization Strategy

## 1. Objective

Provide reliable synchronization between local store databases and the
central server while preserving offline availability and data
integrity.

---

# 2. Architecture

```text
Local Application
      │
      ▼
SQLite
      │
      ▼
Sync Queue
      │
      ▼
Sync Worker
      │
      ▼
Central API
      │
      ▼
Central Database