<?php
/**
 * Migration: Add Storage Unit Fields to Users Table
 * Adds storage unit name, address, latitude, and longitude to users table
 */

namespace StorageUnit\Database\Migrations;

use StorageUnit\Core\Database;

class AddStorageUnitToUsers
{
    public function up()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "
            ALTER TABLE users 
            ADD COLUMN storage_unit_name VARCHAR(255) NULL AFTER name,
            ADD COLUMN storage_unit_address TEXT NULL AFTER storage_unit_name,
            ADD COLUMN storage_unit_latitude DECIMAL(10, 8) NULL AFTER storage_unit_address,
            ADD COLUMN storage_unit_longitude DECIMAL(11, 8) NULL AFTER storage_unit_longitude,
            ADD COLUMN storage_unit_updated_at TIMESTAMP NULL AFTER storage_unit_longitude,
            ADD COLUMN profile_picture VARCHAR(255) NULL AFTER storage_unit_updated_at
        ";

        $conn->exec($sql);
    }

    public function down()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "
            ALTER TABLE users 
            DROP COLUMN storage_unit_name,
            DROP COLUMN storage_unit_address,
            DROP COLUMN storage_unit_latitude,
            DROP COLUMN storage_unit_longitude,
            DROP COLUMN storage_unit_updated_at,
            DROP COLUMN profile_picture
        ";

        $conn->exec($sql);
    }
}
