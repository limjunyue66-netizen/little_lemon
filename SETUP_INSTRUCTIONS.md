# Little Lemon Restaurant - Setup Instructions

## Database Setup Complete!

Your Little Lemon restaurant application has been configured with a complete relational database structure and improved security.

### What's New:

#### Database Tables Created:
1. **users** - User accounts with role management (admin/user)
2. **categories** - Menu item categories
3. **menu** - Food items linked to categories
4. **reservations** - Table reservations with status tracking
5. **orders** - Customer orders with type (dine-in/takeaway)
6. **order_items** - Line items for each order

#### Security Improvements:
- SQL injection prevention using mysqli_real_escape_string()
- Input validation on all forms
- Password hashing with PASSWORD_DEFAULT
- Session management in db.php
- Admin role-based access control
- Error messages and user feedback

#### Features Added:
- Cart functionality for orders
- Menu display by category
- Reservation system with date/time
- Admin dashboard with statistics
- Order tracking
- Category management

---

## Setup Instructions:

### Step 1: Run Database Setup
1. Open your browser
2. Go to: `http://localhost/IT%20programing%20Lim--/little_lemon/setup_database.php`
3. Wait for the success message confirming all tables were created

### Step 2: Delete Setup File
After successful setup, delete the `setup_database.php` file from your project folder.

### Step 3: Create Admin User
To create an admin account, you need to manually insert a user record. Use phpMyAdmin or update your db.php temporarily:

```php
// Add this temporarily to db.php to create an admin:
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
mysqli_query($conn, "INSERT INTO users(name, email, password, role) VALUES('Admin User', 'admin@littlelemon.com', '$admin_pass', 'admin')");
```

Then access the admin panel at: `http://localhost/IT%20programing%20Lim--/little_lemon/admin/dashboard.php`

---

## File Structure:

```
little_lemon/
├── db.php                    (Database connection)
├── index.php                 (Home page)
├── register.php              (User registration)
├── login.php                 (User login)
├── logout.php                (User logout)
├── order.php                 (Place orders)
├── reserve.php               (Make reservations)
├── user_menu.php             (View menu)
├── time_demo.php             (Time demo)
├── setup_database.php        (DELETE AFTER RUNNING)
└── admin/
    ├── dashboard.php         (Admin statistics)
    ├── menu.php              (Manage menu items)
    ├── categories.php        (Manage categories)
    ├── orders.php            (View orders)
    └── reservations.php      (View reservations)
```

---

## Testing the Application:

1. **Register a User**: Go to `/register.php` and create an account
2. **Login**: Use `/login.php` with your credentials
3. **Add Categories**: Go to admin panel and add food categories
4. **Add Menu Items**: Add food items to categories
5. **Place Orders**: Use `/order.php` to order food
6. **Make Reservations**: Use `/reserve.php` to book a table
7. **Admin Dashboard**: View statistics and manage content

---

## Database Schema:

### users
- id (Primary Key)
- name
- email (Unique)
- password (hashed)
- role (admin/user)
- created_at

### categories
- id (Primary Key)
- name

### menu
- id (Primary Key)
- category_id (Foreign Key → categories)
- name
- price
- created_at

### reservations
- id (Primary Key)
- user_id (Foreign Key → users)
- reserve_date
- reserve_time
- guests
- status (pending/confirmed/cancelled)
- created_at

### orders
- id (Primary Key)
- user_id (Foreign Key → users)
- order_type (dine-in/takeaway)
- reservation_id (Foreign Key → reservations, nullable)
- total
- status (pending/completed/cancelled)
- created_at

### order_items
- id (Primary Key)
- order_id (Foreign Key → orders)
- menu_id (Foreign Key → menu)
- quantity

---

## Next Steps:

1. Run setup_database.php
2. Create admin user
3. Add food categories
4. Add menu items
5. Test the ordering and reservation system
6. Customize the UI with your restaurant branding

Good luck with your Little Lemon Restaurant app!
