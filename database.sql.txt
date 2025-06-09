CREATE DATABASE ecommerce;
USE ecommerce;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user'
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    offer DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- to add users or admin 
INSERT INTO users (username, password, role) VALUES ('admin', '$2y$10$Q5J2kW5z5Y5c5K5x5W5y5u5Q5J2kW5z5Y5c5K5x5W5y5u5', 'admin'); 
-- Password: admin123 
-- Do change hash of the password when adding users correctly

INSERT INTO users (username, password, role) VALUES ('user', '$2y$10$Q5J2kW5z5Y5c5K5x5W5y5u5Q5J2kW5z5Y5c5K5x5W5y5u5', 'user'); -- Password: user123

-- Insert sample categories to database for products
INSERT INTO categories (name) VALUES ('Electronics'), ('Clothing');

-- Insert sample products to daabase
INSERT INTO products (title, description, price, offer, category_id, image) VALUES 
('Laptop', 'High-performance laptop', 1000, 800, 1, 'https://some-image-location-or-upload'),
('T-Shirt', 'Comfortable cotton t-shirt', 20, 15, 2, 'https://some-image-location-or-upload');
