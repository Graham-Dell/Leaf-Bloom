# Leaf & Bloom

Leaf & Bloom is a PHP/MySQL tea shop website created for Web Development 1.

## Features

- Database-driven tea products and inventory
- 9 tea products across 3 categories
- User registration and login
- Secure password hashing
- Session-based shopping cart
- Checkout and order placement
- Automatic inventory deduction after orders
- User order history
- Contact form with database storage
- Newsletter subscription
- Admin authentication
- Product CRUD
- Inventory management
- Order status management
- User management
- Contact message management
- Newsletter subscriber management
- PDO prepared statements
- Transaction-based order processing
- Responsive website navigation and UI

## Tea Categories

### Green & White
- Jade Mist Gyokuro
- Silver Needle White
- Dragon Well Longjing

### Black & Oolong
- Golden Darjeeling First Flush
- Lapsang Souchong Smoked
- Tie Guan Yin Oolong

### Botanical Blends
- Rose & Chamomile Bloom
- Hibiscus Elderflower
- Spiced Rooibos Twilight

## Local Setup

1. Install XAMPP with Apache, MySQL, and PHP.
2. Copy the `leafandbloom` folder into `C:\xampp\htdocs\`.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Open phpMyAdmin.
5. Create a database named `leafandbloom`.
6. Import `database/leafandbloom.sql`.
7. Check `includes/db.php` for the local database credentials if necessary.
8. Visit:

   `http://localhost/leafandbloom/`

## Admin

Admin login:

`http://localhost/leafandbloom/admin/login.php`

Use the administrator account stored in the database.

## Database

A final SQL export is included in:

`database/leafandbloom.sql`

The database contains the tables required for products, inventory, users, orders, order items, contact messages, and newsletter subscribers.