<?php
/**
 * Migration Runner
 * Applies database migrations
 */

require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';

use StorageUnit\Database\Migrations\AddStorageUnitToUsers;

try {
    echo "Running migrations...\n";
    
    $migration = new AddStorageUnitToUsers();
    $migration->up();
    
    echo "Migration completed successfully!\n";
    echo "Added storage unit fields and profile picture field to users table.\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
