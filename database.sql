-- Create the ecommerce database and select it
CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

-- Begin updating 'role' column: update if exists, add if missing.
DELIMITER //

CREATE PROCEDURE updateOrAddRoleColumn()
BEGIN
    DECLARE colCount INT DEFAULT 0;
    SELECT COUNT(*) INTO colCount
      FROM INFORMATION_SCHEMA.COLUMNS 
      WHERE table_schema = DATABASE() 
        AND table_name = 'users' 
        AND column_name = 'role';
    IF colCount > 0 THEN
        ALTER TABLE users 
           MODIFY COLUMN role ENUM('user','admin','superadmin') NOT NULL DEFAULT 'user';
    ELSE
        ALTER TABLE users 
           ADD COLUMN role ENUM('user','admin','superadmin') NOT NULL DEFAULT 'user';
    END IF;
END; //

DELIMITER ;

CALL updateOrAddRoleColumn();
DROP PROCEDURE updateOrAddRoleColumn;

-- Table USERS
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('user','admin','superadmin') NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure the users table has a created_at column
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Table CATEGORIES
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table PRODUCTS
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    offer DECIMAL(10,2) NOT NULL,
    category_id INT,
    image VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table CART
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE(user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table ORDERS
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table ORDER_ITEMS
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE(order_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table MESSAGES
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'read') NOT NULL DEFAULT 'sent',
    read_at DATETIME NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Table SETTINGS
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table LOGS
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(255) NOT NULL,
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table CONTENT
CREATE TABLE IF NOT EXISTS content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    body TEXT,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data insertion (updated to include created_at)
INSERT INTO users (username, password, email, role, created_at)
VALUES 
('superadmin', '1234', 's@gmail.com', 'superadmin', NOW()),
('admin', '1234', 'a@gmail.com', 'admin', NOW()),
('user', '1234', 'u@gmail.com', 'user', NOW());

INSERT INTO categories (name) 
VALUES ('Electronics'), ('Clothing');

INSERT INTO products (title, description, price, offer, category_id, image)
VALUES 
('Laptop', 'High-performance laptop', 1000, 800, 1, 'https://some-image-location-or-upload');
