<?php
/**
 * Migration Runner
 * Applies database migrations
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';

// Include the migration class directly
require_once __DIR__ . '/../app/Database/Migrations/add_storage_unit_to_users.php';

use StorageUnit\Database\Migrations\AddStorageUnitToUsers;

try {
    echo "Running migrations...\n";
    echo "Note: This script should be run inside Docker container or with Docker database accessible.\n";
    
    $migration = new AddStorageUnitToUsers();
    $migration->up();
    
    echo "Migration completed successfully!\n";
    echo "Added storage unit fields and profile picture field to users table.\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    echo "Make sure Docker database is running and accessible.\n";
    exit(1);
}
