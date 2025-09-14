-- Test Database Schema for Storage Unit Management System
-- This file creates a test database with the same structure as production

-- Create test database (uncomment if needed)
-- CREATE DATABASE storageunit_test;
-- USE storageunit_test;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Items table for storage inventory
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    qty INT DEFAULT 1,
    user_id INT NOT NULL,
    img VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_title (title)
);

-- Insert test data
INSERT INTO users (email, password, name) VALUES 
('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User'),
('admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User');

-- Insert test items
INSERT INTO items (title, description, qty, user_id, img) VALUES 
('Test Item 1', 'Test description for item 1', 1, 1, 'test1.jpg'),
('Test Item 2', 'Test description for item 2', 2, 1, 'test2.jpg'),
('Admin Item', 'Admin test item', 1, 2, 'admin.jpg');
