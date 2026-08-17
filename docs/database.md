
---

# 7. `docs/database.md`

```md
# Database Design

## 1. Local Database

The local POS uses SQLite.

The database must support complete local operation without Internet
connectivity.

---

# 2. Main Entities

Initial entities:

```text
stores
users
devices

categories
products
customers

sales
sale_items
payments

stock
stock_movements

cash_sessions

sync_queue
sync_logs