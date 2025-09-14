<?php
/**
 * Base Test Case
 * Provides common functionality for all tests
 */

namespace StorageUnit\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use StorageUnit\Core\Database;
use PDO;

abstract class TestCase extends PHPUnitTestCase
{
    protected $pdo;
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        $_ENV['APP_ENV'] = 'testing';
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_NAME'] = 'storageunit_test';
        $_ENV['DB_USER'] = 'root';
        $_ENV['DB_PASS'] = '';
        $_ENV['APP_DEBUG'] = 'true';
        
        // Include config and constants
        require_once __DIR__ . '/../config/app/config.php';
        require_once __DIR__ . '/../config/app/constants.php';
        
        // Set up test database connection
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=storageunit_test;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        // Clear and recreate test data
        $this->resetDatabase();
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        $this->resetDatabase();
        parent::tearDown();
    }

    /**
     * Reset database to clean state
     */
    protected function resetDatabase()
    {
        // Clear all data
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->pdo->exec('TRUNCATE TABLE item_tags');
        $this->pdo->exec('TRUNCATE TABLE items');
        $this->pdo->exec('TRUNCATE TABLE categories');
        $this->pdo->exec('TRUNCATE TABLE locations');
        $this->pdo->exec('TRUNCATE TABLE tags');
        $this->pdo->exec('TRUNCATE TABLE users');
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        
        // Insert test data
        $this->pdo->exec("
            INSERT INTO users (id, email, password, name) VALUES 
            (1, 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User'),
            (2, 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User')
        ");
        
        // Insert test categories
        $this->pdo->exec("
            INSERT INTO categories (id, name, color, icon, user_id) VALUES 
            (1, 'Tools', '#28a745', 'fas fa-tools', 1),
            (2, 'Electronics', '#007bff', 'fas fa-laptop', 1),
            (3, 'General', '#6c757d', 'fas fa-box', 1),
            (4, 'Tools', '#28a745', 'fas fa-tools', 2)
        ");
        
        // Insert test locations
        $this->pdo->exec("
            INSERT INTO locations (id, name, parent_id, user_id) VALUES 
            (1, 'Garage', NULL, 1),
            (2, 'Workbench', 1, 1),
            (3, 'Storage Room', NULL, 1),
            (4, 'Basement', NULL, 2)
        ");
        
        // Insert test tags
        $this->pdo->exec("
            INSERT INTO tags (id, name, user_id) VALUES 
            (1, 'power-tools', 1),
            (2, 'hand-tools', 1),
            (3, 'safety', 1),
            (4, 'precision', 1)
        ");
        
        $this->pdo->exec("
            INSERT INTO items (id, title, description, qty, user_id, category_id, location_id, img) VALUES 
            (1, 'Test Item 1', 'Test description for item 1', 1, 1, 1, 2, 'test1.jpg'),
            (2, 'Test Item 2', 'Test description for item 2', 2, 1, 2, 3, 'test2.jpg'),
            (3, 'Admin Item', 'Admin test item', 1, 2, 4, 4, 'admin.jpg')
        ");
        
        // Insert test item tags
        $this->pdo->exec("
            INSERT INTO item_tags (item_id, tag_id) VALUES 
            (1, 1), (1, 2),  -- Test Item 1: power-tools, hand-tools
            (2, 2), (2, 4)    -- Test Item 2: hand-tools, precision
        ");
    }

    /**
     * Create a test user
     */
    protected function createTestUser($email = 'test@example.com', $name = 'Test User', $password = 'password123')
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashedPassword, $name]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Create a test item
     */
    protected function createTestItem($title = 'Test Item', $description = 'Test Description', $qty = 1, $userId = 1, $img = null, $categoryId = null, $locationId = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO items (title, description, qty, user_id, category_id, location_id, img) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $qty, $userId, $categoryId, $locationId, $img]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Create a test category
     */
    protected function createTestCategory($name = 'Test Category', $color = '#007bff', $icon = 'fas fa-box', $userId = 1)
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (name, color, icon, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $color, $icon, $userId]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Create a test location
     */
    protected function createTestLocation($name = 'Test Location', $parentId = null, $userId = 1)
    {
        $stmt = $this->pdo->prepare("INSERT INTO locations (name, parent_id, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $parentId, $userId]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Create a test tag
     */
    protected function createTestTag($name = 'test-tag', $userId = 1)
    {
        $stmt = $this->pdo->prepare("INSERT INTO tags (name, user_id) VALUES (?, ?)");
        $stmt->execute([$name, $userId]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Set up authenticated session
     */
    protected function authenticateUser($userId = 1)
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = 'Test User';
        $_SESSION['user_email'] = 'test@example.com';
    }

    /**
     * Clear session
     */
    protected function clearSession()
    {
        $_SESSION = [];
    }
}
