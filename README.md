<<<<<<< HEAD
Ecommerce Website Project
This is a simple ecommerce website built using PHP and MySQL as part of a web development class assignment. It includes user and admin functionalities such as product listing, product details, cart system, and admin management for products and categories.
Features

User Side:
Login/Logout
Browse products by category
View product details
Add products to cart
View cart


Admin Side:
Login/Logout
Add, edit, and delete products
Add and delete categories



Prerequisites

PHP 7.4 or higher
MySQL
A web server (e.g., Apache) - XAMPP or WAMP recommended
Git (to clone this repository)

Setup Instructions

Clone the Repository:
git clone <repository-url>
cd ecommerce


Set Up the Database:

Create a MySQL database named ecommerce.
Import the database.sql file into your database:mysql -u your_username -p ecommerce < database.sql


This will create the necessary tables and insert sample data.


Configure Database Connection:

Rename config.sample.php to config.php.
Open config.php and update the database credentials ($username and $password) to match your MySQL setup.


Set Up the Web Server:

Place the ecommerce folder in your web server's root directory (e.g., htdocs for XAMPP).
Ensure the uploads folder is writable by the server (e.g., chmod 777 uploads on Linux).


Access the Website:

Start your web server and MySQL.
Open your browser and go to http://localhost/ecommerce/.
User Login: Username: user, Password: user123
Admin Login: Username: admin, Password: admin123



Project Structure

config.php: Database connection (excluded from Git, use config.sample.php)
login.php: Login page for users and admins
index.php: Homepage (redirects to login or products)
products.php: Product listing page
product_details.php: Product details page
cart.php: Handles adding items to the cart
view_cart.php: Displays cart items
logout.php: Logs out the user
admin/: Admin-related pages
index.php: Admin dashboard
add_product.php: Add a new product
manage_products.php: Manage existing products
add_category.php: Add or delete categories


uploads/: Folder for storing product images
database.sql: SQL script to set up the database

Notes

The uploads folder is excluded from the repository (via .gitignore). You need to create it manually.
Sample product images are placeholder URLs. You can upload real images via the admin panel.

Screenshots
(You can add screenshots here if you wish, e.g., by taking screenshots of the website and uploading them to GitHub.)
Author

[Your Name]

Submission Date

June 09, 2025

=======


# E-commerce Website Project

This is a basic e-commerce website developed using PHP and MySQL as part of a web development course assignment. It features both user and admin functionalities, including product browsing, detailed product views, shopping cart operations, and full product/category management for administrators.

---

## 🌐 Features

### User Side:

* User Login/Logout
* Browse products by category
* View product details
* Add products to shopping cart
* View items in cart

### Admin Side:

* Admin Login/Logout
* Add new products
* Edit or delete existing products
* Add and delete categories

---

## 🛠️ Prerequisites

* PHP 7.4 or higher
* MySQL
* A web server (e.g., Apache) – recommended: XAMPP or WAMP
* Git (for cloning the repository)

---

## ⚙️ Setup Instructions

### 1. Clone the Repository

```bash
git clone <repository-url>
cd ecommerce
```

### 2. Set Up the Database

* Create a MySQL database named `ecommerce`
* Import the SQL file:

```bash
mysql -u your_username -p ecommerce < database.sql
```

> This will create the necessary tables and populate them with sample data.

### 3. Configure Database Connection

* Rename `config.sample.php` to `config.php`
* Open `config.php` and update your MySQL credentials (`$username` and `$password`)

### 4. Set Up the Web Server

* Place the `ecommerce` folder in your web server's root directory (e.g., `htdocs` for XAMPP)
* Ensure the `uploads` folder is writable:

### 5. Access the Website

* Start your web server and MySQL
* Open a browser and visit:
  `http://localhost/ecommerce/`

**Login Credentials:**

* **User:**
  Username: `user`
  Password: `user123`
* **Admin:**
  Username: `admin`
  Password: `admin123`

---

## 📁 Project Structure

```
ecommerce/
│
├── config.php                 # Database connection file (use config.sample.php as template)
├── login.php                  # Login page for both users and admins
├── index.php                  # Homepage / redirect logic
├── products.php               # Product listing for users
├── product_details.php        # Product detail view
├── cart.php                   # Logic for adding items to cart
├── view_cart.php              # Cart overview
├── logout.php                 # Logout script
│
├── admin/                     # Admin panel folder
│   ├── index.php              # Admin dashboard
│   ├── add_product.php        # Add product page
│   ├── manage_products.php    # View/update/delete products
│   └── add_category.php       # Add or delete categories
│
├── uploads/                   # Folder for product images (create manually)
├── database.sql               # SQL script for creating the database
```

---

## 📝 Notes

* The `uploads/` folder is excluded from Git versioning via `.gitignore`; make sure to create it manually.
* Sample images are placeholders. You can upload real product images via the admin panel.

---

## 📷 Screenshots

*(You may add screenshots here to showcase the website UI. Upload them to your GitHub repository or include inline in README.md)*

---

## 👤 Author

**Mayan Roy**

---

## 📅 Submission Date

**June 09, 2025**

---
>>>>>>> 1419a3ae286267232a590a4ae362f48ed9b55993
