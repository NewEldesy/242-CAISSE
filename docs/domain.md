
---

# 6. `docs/domain.md`

Ici, on décrit **le métier**, pas la technologie.

```md
# Domain Model

## 1. Store

A store represents a physical retail location.

Attributes:

- id
- name
- code
- status
- created_at
- updated_at

A store can have:

- users;
- devices;
- products;
- sales;
- stock;
- customers.

---

# 2. User

A user represents a person authorized to use the system.

Possible roles:

- store_user;
- store_manager;
- central_admin.

Users must have permissions appropriate to their role.

---

# 3. Device

A device represents an installation or terminal used by a store.

A device belongs to one store.

The device must have a globally unique identifier.

The device identifier is important for synchronization and auditing.

---

# 4. Product

A product represents an item sold by the store.

Possible attributes:

- id;
- reference;
- name;
- description;
- category;
- selling price;
- purchase price;
- status.

Products may later be synchronized from the central system to stores.

---

# 5. Category

A category groups products.

Examples:

- Food
- Drinks
- Electronics
- Clothing

The final categories depend on the business requirements.

---

# 6. Customer

A customer represents a buyer.

Customer information must be kept minimal and appropriate to the
business requirements.

---

# 7. Sale

A sale represents a completed or otherwise recorded transaction.

A sale contains:

- unique identifier;
- store;
- device;
- user;
- date/time;
- status;
- total amount.

A sale contains one or more sale items.

---

# 8. Sale Item

A sale item represents one product included in a sale.

Contains:

- product;
- quantity;
- unit price;
- subtotal.

The sale item must preserve the price used at the time of sale.

Changing the current product price must not change historical sales.

---

# 9. Payment

A payment represents money received for a sale.

Possible payment methods may include:

- cash;
- mobile money;
- card;
- other methods defined by the business.

The final list must be confirmed before implementation.

---

# 10. Stock

Stock represents the quantity of a product available in a store.

Stock changes through stock movements.

Examples:

- sale;
- purchase;
- manual adjustment;
- return;
- cancellation.

---

# 11. Stock Movement

A stock movement records a change in quantity.

Examples:

```text
SALE
PURCHASE
ADJUSTMENT
RETURN
CANCELLATION