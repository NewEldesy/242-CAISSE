# Product Definition

## 1. Product Overview

The application is a multi-store management system designed for small
and medium-sized retail businesses.

Each store must have a local application capable of operating without
Internet connectivity.

A central server will later provide:

- centralized data;
- multi-store management;
- reporting;
- monitoring;
- synchronization;
- administration.

---

# 2. Main Objective

Provide a reliable management and point-of-sale system that allows each
store to continue operating even when the Internet connection is
unavailable.

---

# 3. Users

Initial roles:

## Store User

Can:

- access the local POS;
- create sales;
- consult products;
- consult stock;
- manage permitted operations.

## Store Manager

Can:

- manage products;
- manage stock;
- consult reports;
- manage store users;
- perform management operations according to permissions.

## Central Administrator

Will later be able to:

- manage stores;
- manage users;
- monitor synchronization;
- consult consolidated data;
- manage products;
- access global reports.

---

# 4. Core Features

## Local POS

- authentication;
- product catalog;
- categories;
- customers;
- cart;
- sales;
- payments;
- stock;
- cash sessions;
- reports.

## Synchronization

- queue local changes;
- synchronize asynchronously;
- retry failed operations;
- prevent duplicates;
- record synchronization errors;
- monitor synchronization status.

## Central System

Future features:

- store management;
- user management;
- centralized reporting;
- synchronization monitoring;
- consolidated sales;
- consolidated stock.

---

# 5. Offline Requirement

The local POS must continue to operate without Internet access.

At minimum, the following must work offline:

- authentication using locally available credentials;
- product consultation;
- sales;
- payment recording;
- stock updates;
- local reports;
- synchronization queue.

---

# 6. Online Requirement

When connectivity is available, the application should synchronize
pending operations automatically or through a controlled synchronization
process.

The local user must not be required to manually recreate operations
after an Internet outage.

---

# 7. Non-Functional Requirements

The system must prioritize:

1. Data integrity
2. Reliability
3. Security
4. Offline availability
5. Synchronization reliability
6. Maintainability
7. Observability
8. Performance

---

# 8. Important Business Principle

The local store must be able to operate independently.

The central server enhances management and visibility but must not be a
single point of failure for basic store operations.