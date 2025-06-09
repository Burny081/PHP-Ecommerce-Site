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

