# System Architecture

## 1. Architecture Style

The system follows an offline-first distributed architecture.

Each store is an independent local node.

The central server is responsible for centralized management and
synchronization.

---

# 2. High-Level Architecture

```text
                     CENTRAL SYSTEM
             ┌─────────────────────────┐
             │ Laravel API             │
             │ Central Database        │
             │ Dashboard               │
             └────────────┬────────────┘
                          │
                    Sync Contract
                          │
          ┌───────────────┼───────────────┐
          │               │               │
          ▼               ▼               ▼
      STORE A          STORE B          STORE C
    ┌─────────┐       ┌─────────┐      ┌─────────┐
    │ POS     │       │ POS     │      │ POS     │
    │ SQLite  │       │ SQLite  │      │ SQLite  │
    │ Sync    │       │ Sync    │      │ Sync    │
    └─────────┘       └─────────┘      └─────────┘