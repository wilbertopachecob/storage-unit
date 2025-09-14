<?php
/**
 * Test Database Setup
 * Creates and populates the test database
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';

use StorageUnit\Core\Database;

try {
    echo "Setting up test database...\n";
    
    // Connect to MySQL without specifying database
    $pdo = new PDO(
        'mysql:host=localhost;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Create test database
    $pdo->exec('CREATE DATABASE IF NOT EXISTS storageunit_test');
    $pdo->exec('USE storageunit_test');
    
    // Read and execute the main database schema
    $schema = file_get_contents(__DIR__ . '/../config/database/database.sql');
    $pdo->exec($schema);
    
    // Run the migration to add new fields
    $migration = new \StorageUnit\Database\Migrations\AddStorageUnitToUsers();
    $migration->up();
    
    echo "Test database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Test database setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
