# Little Lemon Restaurant - Entity Relationship Diagram (ERD)

## Database Schema Overview

```
┌─────────────────────┐
│       USERS         │
├─────────────────────┤
│ id (PK)             │
│ name                │
│ email (UNIQUE)      │
│ password            │
│ role                │
│ created_at          │
└──────┬──────────────┘
       │
       │ 1:N
       │
       ├─────────────────────────────────┐
       │                                 │
       ▼                                 ▼
┌─────────────────────┐        ┌──────────────────────┐
│     ORDERS          │        │   RESERVATIONS       │
├─────────────────────┤        ├──────────────────────┤
│ id (PK)             │        │ id (PK)              │
│ user_id (FK)        │        │ user_id (FK)         │
│ order_type          │        │ reserve_date         │
│ reservation_id (FK) │        │ reserve_time         │
│ total               │        │ guests               │
│ status              │        │ status               │
│ created_at          │        │ created_at           │
└──────┬──────────────┘        └──────────────────────┘
       │
       │ 1:N
       │
       ▼
┌─────────────────────┐
│   ORDER_ITEMS       │
├─────────────────────┤
│ id (PK)             │
│ order_id (FK)       │
│ menu_id (FK)        │
│ quantity            │
└──────┬──────────────┘
       │
       │ N:1
       │
       ▼
┌─────────────────────┐
│       MENU          │
├─────────────────────┤
│ id (PK)             │
│ category_id (FK)    │
│ name                │
│ price               │
│ created_at          │
└──────┬──────────────┘
       │
       │ N:1
       │
       ▼
┌──────────────────────┐
│   CATEGORIES         │
├──────────────────────┤
│ id (PK)              │
│ name                 │
│ created_at           │
└──────────────────────┘
```

---

## Table Definitions

### 1. **USERS**
Store customer and admin user accounts.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO_INCREMENT | Unique user ID |
| name | VARCHAR(100) | NOT NULL | User's full name |
| email | VARCHAR(100) | UNIQUE, NOT NULL | User's email |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| role | ENUM('admin','user') | DEFAULT 'user' | User role |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Account creation date |

---

### 2. **CATEGORIES**
Food item categories (Staple Food, Drinks, Desserts, etc.).

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO_INCREMENT | Unique category ID |
| name | VARCHAR(50) | NOT NULL | Category name |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation date |

---

### 3. **MENU**
Food items linked to categories.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO_INCREMENT | Unique menu item ID |
| category_id | INT | NOT NULL, FK(CATEGORIES) | Category reference |
| name | VARCHAR(100) | NOT NULL | Food item name |
| price | DECIMAL(6,2) | NOT NULL | Item price |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation date |

---

### 4. **ORDERS**
Customer orders with type (dine-in/takeaway) and status.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO_INCREMENT | Unique order ID |
| user_id | INT | NOT NULL, FK(USERS) | Customer reference |
| order_type | ENUM('dine-in','takeaway') | NOT NULL | Order type |
| reservation_id | INT | FK(RESERVATIONS), NULL | Linked reservation |
| total | DECIMAL(8,2) | NOT NULL | Order total amount |
| status | ENUM('pending','completed','cancelled') | DEFAULT 'pending' | Order status |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Order date/time |

---

### 5. **ORDER_ITEMS**
Line items for each order (quantity of each menu item).

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO_INCREMENT | Unique line item ID |
| order_id | INT | NOT NULL, FK(ORDERS) | Order reference |
| menu_id | INT | NOT NULL, FK(MENU) | Menu item reference |
| quantity | INT | NOT NULL | Quantity ordered |

---

### 6. **RESERVATIONS**
Table reservations with status tracking.

| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PK, AUTO_INCREMENT | Unique reservation ID |
| user_id | INT | NOT NULL, FK(USERS) | Customer reference |
| reserve_date | DATE | NOT NULL | Reservation date |
| reserve_time | TIME | NOT NULL | Reservation time |
| guests | INT | NOT NULL | Number of guests |
| status | ENUM('pending','confirmed','cancelled') | DEFAULT 'pending' | Reservation status |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Booking date/time |

---

## Relationships

### One-to-Many (1:N)

1. **USERS → ORDERS** (1:N)
   - A user can have many orders
   - Foreign Key: `orders.user_id` → `users.id`

2. **USERS → RESERVATIONS** (1:N)
   - A user can make many reservations
   - Foreign Key: `reservations.user_id` → `users.id`

3. **ORDERS → ORDER_ITEMS** (1:N)
   - An order can have many line items
   - Foreign Key: `order_items.order_id` → `orders.id`

4. **CATEGORIES → MENU** (1:N)
   - A category can have many menu items
   - Foreign Key: `menu.category_id` → `categories.id`

5. **RESERVATIONS → ORDERS** (Optional 1:N)
   - A reservation can be linked to an order
   - Foreign Key: `orders.reservation_id` → `reservations.id` (NULL allowed)

### Many-to-One (N:1)

1. **MENU ← CATEGORIES** (N:1)
   - Many menu items belong to one category

2. **ORDER_ITEMS ← MENU** (N:1)
   - Many order items reference one menu item

---

## Sample Data Flow

### Customer Orders Food
```
User (id=1) 
  → Creates Order (user_id=1, status='pending')
    → Creates ORDER_ITEMS (order_id=1, menu_id=3, qty=2)
    → ORDER_ITEMS links to MENU (id=3, name='Nasi Lemak')
    → MENU links to CATEGORY (id=1, name='Staple Food')
```

### Customer Makes Reservation
```
User (id=1)
  → Creates RESERVATION (user_id=1, date='2026-02-10', time='19:00')
  → Can link to ORDERS (reservation_id=reservation_id)
```

---

## Key Features

✅ **Normalized Structure** — No data redundancy  
✅ **Foreign Key Constraints** — Data integrity  
✅ **Cascade Delete** — ON DELETE CASCADE for related records  
✅ **Status Tracking** — Order and reservation status management  
✅ **Timestamps** — created_at for audit trail  
✅ **Role-Based Access** — Admin vs User roles  

---

## SQL Schema Export

See `setup_database.php` for complete CREATE TABLE statements.
