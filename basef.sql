-- Création de la base de données
CREATE DATABASE ecommerce;
USE ecommerce;

-- Table USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    email VARCHAR(30) NOT NULL
);

-- Table CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Table PRODUCTS (avec status ajouté)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    offer DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image VARCHAR(255) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'in_stock',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table CART
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE(user_id, product_id)
);

-- Table ORDERS
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table ORDER_ITEMS
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE(order_id, product_id)
);

-- Insertion de comptes utilisateurs de test
INSERT INTO users (username, password, role, email)
VALUES 
('admin', '1234', 'admin', 'a@gmail.com'),
('user', '1234', 'user', 'u@gmail.com');

-- Insertion de catégories
INSERT INTO categories (name) VALUES 
('Electronics'),
('Clothing');

-- Insertion de produits (avec status implicite = 'in_stock')
INSERT INTO products (title, description, price, offer, category_id, image)
VALUES 
('Laptop', 'High-performance laptop', 1000, 800, 1, 'https://some-image-location-or-upload'),
('T-Shirt', 'Comfortable cotton t-shirt', 20, 15, 2, 'https://some-image-location-or-upload');
