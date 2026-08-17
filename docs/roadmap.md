
---

# 10. `docs/roadmap.md`

```md
# Project Roadmap

## Phase 0 — Product Discovery

Status: TODO

- [ ] Define actors
- [ ] Define business requirements
- [ ] Define business rules
- [ ] Validate core workflows
- [ ] Identify critical operations

---

# Phase 1 — Architecture

Status: TODO

- [ ] Define global architecture
- [ ] Define local architecture
- [ ] Define central architecture
- [ ] Define technology stack
- [ ] Define distributed identifiers
- [ ] Define synchronization boundaries

---

# Phase 2 — Synchronization Contract

Status: TODO

- [ ] Define synchronized entities
- [ ] Define event structure
- [ ] Define event IDs
- [ ] Define operations
- [ ] Define acknowledgement
- [ ] Define idempotency
- [ ] Define error categories
- [ ] Define retry strategy
- [ ] Define versioning
- [ ] Define initial conflict strategy

---

# Phase 3 — Local Database

Status: TODO

- [ ] Initialize SQLite
- [ ] Create stores
- [ ] Create users
- [ ] Create devices
- [ ] Create categories
- [ ] Create products
- [ ] Create customers
- [ ] Create sales
- [ ] Create sale items
- [ ] Create payments
- [ ] Create stock
- [ ] Create stock movements
- [ ] Create cash sessions
- [ ] Create sync queue
- [ ] Create sync logs

---

# Phase 4 — Local POS

Status: TODO

- [ ] Authentication
- [ ] Product management
- [ ] Category management
- [ ] Customer management
- [ ] Stock management
- [ ] Cart
- [ ] Sales
- [ ] Payments
- [ ] Cash sessions
- [ ] Reports

---

# Phase 5 — Local Sync Engine

Status: TODO

- [ ] Create sync queue service
- [ ] Create sync worker
- [ ] Create retry mechanism
- [ ] Create synchronization states
- [ ] Create error logging
- [ ] Create synchronization monitoring

---

# Phase 6 — Offline Testing

Status: TODO

- [ ] Sale without Internet
- [ ] Multiple sales without Internet
- [ ] Application restart
- [ ] Network reconnection
- [ ] Synchronization retry
- [ ] Network failure during request
- [ ] Duplicate event
- [ ] Invalid event
- [ ] Recovery from failed synchronization

---

# Phase 7 — Central API

Status: TODO

- [ ] Initialize Laravel
- [ ] Configure central database
- [ ] Authentication
- [ ] Stores
- [ ] Devices
- [ ] Sync endpoint
- [ ] Idempotency
- [ ] Sync acknowledgement
- [ ] Sync logs
- [ ] API tests

---

# Phase 8 — Central Dashboard

Status: TODO

- [ ] Dashboard
- [ ] Stores
- [ ] Sales
- [ ] Products
- [ ] Stock
- [ ] Users
- [ ] Synchronization status
- [ ] Synchronization errors
- [ ] Reports

---

# Phase 9 — Bidirectional Synchronization

Status: TODO

- [ ] Central → Store synchronization
- [ ] Product updates
- [ ] Price updates
- [ ] User updates
- [ ] Conflict detection
- [ ] Conflict resolution
- [ ] Versioning

---

# Phase 10 — Production

Status: TODO

- [ ] Server deployment
- [ ] Database backup
- [ ] Monitoring
- [ ] Logging
- [ ] Security hardening
- [ ] Update mechanism
- [ ] Disaster recovery
- [ ] Documentation
- [ ] Installation procedure
- [ ] Maintenance procedure