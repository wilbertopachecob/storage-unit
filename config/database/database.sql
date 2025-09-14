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

-- Categories table for item organization
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7) DEFAULT '#007bff',
    icon VARCHAR(50) DEFAULT 'fas fa-box',
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    UNIQUE KEY unique_category_user (name, user_id)
);

-- Locations table for storage organization
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES locations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_parent_id (parent_id)
);

-- Items table for storage inventory
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    qty INT DEFAULT 1,
    user_id INT NOT NULL,
    category_id INT NULL,
    location_id INT NULL,
    img VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_title (title),
    INDEX idx_category_id (category_id),
    INDEX idx_location_id (location_id)
);

-- Tags table for flexible item tagging
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    UNIQUE KEY unique_tag_user (name, user_id)
);

-- Item tags junction table
CREATE TABLE item_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_item_tag (item_id, tag_id)
);

-- Insert sample data for testing (optional)
-- Note: Password is 'password123' hashed with password_hash()
INSERT INTO users (email, password, name) VALUES 
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User'),
('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User');

-- Insert sample categories
INSERT INTO categories (name, color, icon, user_id) VALUES 
('Tools', '#28a745', 'fas fa-tools', 1),
('Electronics', '#007bff', 'fas fa-laptop', 1),
('Safety Equipment', '#dc3545', 'fas fa-hard-hat', 1),
('Measuring Tools', '#ffc107', 'fas fa-ruler', 1),
('General', '#6c757d', 'fas fa-box', 1),
('Tools', '#28a745', 'fas fa-tools', 2),
('Measuring Tools', '#ffc107', 'fas fa-ruler', 2);

-- Insert sample locations
INSERT INTO locations (name, parent_id, user_id) VALUES 
('Garage', NULL, 1),
('Workbench', 1, 1),
('Tool Cabinet', 1, 1),
('Storage Room', NULL, 1),
('Shelf A', 4, 1),
('Shelf B', 4, 1),
('Basement', NULL, 2),
('Work Area', 7, 2);

-- Insert sample tags
INSERT INTO tags (name, user_id) VALUES 
('power-tools', 1),
('hand-tools', 1),
('safety', 1),
('precision', 1),
('woodworking', 1),
('construction', 1),
('measuring', 2),
('precision', 2);

-- Insert sample items with categories and locations
INSERT INTO items (title, description, qty, user_id, category_id, location_id, img) VALUES 
('Power Drill', 'Cordless power drill with multiple attachments', 1, 1, 1, 2, '5c3baf8dcb438p-311-124-2132_lrg.jpg'),
('Table Saw', 'Professional table saw for woodworking', 1, 1, 1, 3, 'table_saw.jpg'),
('Safety Gloves', 'Heavy-duty work gloves for construction', 3, 1, 3, 5, 'cutter.jpg'),
('Measuring Tape', '25-foot retractable measuring tape', 2, 2, 4, 8, 'mallet.jpg'),
('Level Tool', 'Professional level for construction projects', 1, 2, 4, 8, '5c3c1c7c32cf45c3c029174f51levels.jpg');

-- Insert sample item tags
INSERT INTO item_tags (item_id, tag_id) VALUES 
(1, 1), (1, 5), (1, 6),  -- Power Drill: power-tools, woodworking, construction
(2, 1), (2, 5), (2, 6),  -- Table Saw: power-tools, woodworking, construction
(3, 3), (3, 6),          -- Safety Gloves: safety, construction
(4, 2), (4, 7), (4, 8),  -- Measuring Tape: hand-tools, measuring, precision
(5, 2), (5, 7), (5, 8);  -- Level Tool: hand-tools, measuring, precision
