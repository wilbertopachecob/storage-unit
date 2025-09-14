-- Storage Unit Management System Database Schema
-- Created for PHP-based inventory management application

-- Create database (uncomment if needed)
-- CREATE DATABASE storageunit;
-- USE storageunit;

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

-- Insert sample data for testing (optional)
-- Note: Password is 'password123' hashed with password_hash()
INSERT INTO users (email, password, name) VALUES 
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User'),
('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User');

-- Insert sample items
INSERT INTO items (title, description, qty, user_id, img) VALUES 
('Power Drill', 'Cordless power drill with multiple attachments', 1, 1, '5c3baf8dcb438p-311-124-2132_lrg.jpg'),
('Table Saw', 'Professional table saw for woodworking', 1, 1, 'table_saw.jpg'),
('Safety Gloves', 'Heavy-duty work gloves for construction', 3, 1, 'cutter.jpg'),
('Measuring Tape', '25-foot retractable measuring tape', 2, 2, 'mallet.jpg'),
('Level Tool', 'Professional level for construction projects', 1, 2, '5c3c1c7c32cf45c3c029174f51levels.jpg');
