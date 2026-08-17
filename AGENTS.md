
---

# 2. `AGENTS.md`

Celui-ci est **très important**.

C'est le fichier qui sert de règlement général pour les agents IA.

```md
# AGENTS.md

## Role

You are an AI software development agent working on the
Multi-Store Management System.

Your responsibility is to implement tasks while preserving:

- business rules;
- application architecture;
- offline-first behavior;
- synchronization compatibility;
- security;
- data integrity;
- testability;
- maintainability.

Do not make architectural decisions silently.

---

# 1. General Rules

Before modifying code:

1. Read `README.md`.
2. Read the relevant documentation in `docs/`.
3. Understand the existing implementation.
4. Identify the files that need to change.
5. Explain the implementation plan when the task is non-trivial.
6. Implement only the requested scope.
7. Run relevant tests.
8. Report what changed.

Never rewrite unrelated parts of the application.

---

# 2. Scope Control

One task should produce one coherent change.

Do not:

- refactor unrelated code;
- rename unrelated classes;
- change the architecture without approval;
- add unnecessary dependencies;
- introduce features that were not requested;
- modify synchronization behavior accidentally.

If an improvement is discovered outside the current task:

1. mention it;
2. do not implement it;
3. create or suggest a separate task.

---

# 3. Offline-First Rules

The local application must remain functional without Internet access.

Never make a normal business operation depend on:

- the central API;
- an external server;
- a remote database;
- network availability.

A sale must be possible while offline.

Network synchronization is asynchronous and must never block the
core local business operation.

---

# 4. Synchronization Rules

Any modification involving:

- products;
- stock;
- sales;
- sale items;
- payments;
- customers;
- users;
- stores;
- devices;
- synchronization;

MUST be evaluated against:

`docs/sync-contract.md`

and, when relevant:

`docs/sync-strategy.md`

Never change the synchronization format without updating the
corresponding documentation and tests.

---

# 5. Identifiers

Entities participating in synchronization MUST use identifiers
compatible with distributed operation.

Do not introduce assumptions based on sequential local IDs when the
entity is synchronized.

Before changing an identifier:

1. inspect the synchronization contract;
2. inspect existing relationships;
3. inspect tests;
4. evaluate migration impact.

---

# 6. Data Integrity

Business-critical operations must be atomic whenever appropriate.

Examples:

Creating a sale may involve:

- creating the sale;
- creating sale items;
- registering payment;
- updating stock;
- creating synchronization records.

These operations must not leave the local database in an inconsistent
state.

---

# 7. Idempotency

Synchronization operations must be safe to retry.

The same synchronization event must not create duplicate business
operations.

Never implement a synchronization endpoint that assumes the network
will deliver a request exactly once.

---

# 8. Error Handling

Failures must be explicit.

Do not silently ignore:

- database errors;
- synchronization errors;
- validation errors;
- authorization errors;
- network failures.

Errors that affect synchronization must be logged according to the
synchronization strategy.

---

# 9. Testing

Every new business feature must include appropriate tests.

For synchronization-related features, test at least:

- success;
- retry;
- duplicate request;
- network failure;
- invalid payload;
- partial failure;
- already synchronized event.

---

# 10. Security

Never trust:

- user input;
- local identifiers;
- synchronization payloads;
- client-provided permissions;
- remote data.

Authentication and authorization must be enforced independently.

Never expose:

- passwords;
- secrets;
- API tokens;
- private credentials;

in source code or logs.

---

# 11. Database

Do not manually modify the database schema outside the project's
migration mechanism.

Any schema modification must include:

- migration;
- affected models;
- affected relationships;
- tests when relevant;
- synchronization impact analysis when relevant.

---

# 12. Git

Prefer small, focused commits.

Recommended format:

`feat: add local sales management`

`fix: prevent duplicate synchronization events`

`test: add offline sales tests`

`refactor: extract sync queue service`

Do not mix unrelated changes in the same commit.

---

# 13. When Requirements Are Ambiguous

Do not guess when an ambiguity could affect:

- money;
- stock;
- synchronization;
- security;
- permissions;
- data integrity.

Instead:

1. identify the ambiguity;
2. explain its impact;
3. propose options;
4. wait for a decision when necessary.

---

# 14. Documentation

When implementation changes an architectural rule, update the relevant
documentation.

Documentation is part of the system.

Do not allow code and architecture documentation to diverge silently.