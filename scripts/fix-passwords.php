<?php
/**
 * Fix Passwords Script
 * Updates all user passwords to use the current password hashing method
 */

require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

use StorageUnit\Core\Database;
use StorageUnit\Core\Security;

echo "🔐 Fixing user passwords...\n";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Get all users
    $sql = "SELECT id, email, name FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll();

    if (empty($users)) {
        echo "❌ No users found in database\n";
        exit(1);
    }

    echo "📋 Found " . count($users) . " users\n";

    // Update each user's password
    $updateSql = "UPDATE users SET password = :password WHERE id = :id";
    $updateStmt = $conn->prepare($updateSql);

    foreach ($users as $user) {
        $newHash = Security::hashPassword('password123');
        
        $result = $updateStmt->execute([
            ':password' => $newHash,
            ':id' => $user['id']
        ]);

        if ($result) {
            echo "✅ Updated password for: {$user['email']} ({$user['name']})\n";
        } else {
            echo "❌ Failed to update password for: {$user['email']}\n";
        }
    }

    echo "\n🎉 Password fix completed!\n";
    echo "📋 All users now have password: password123\n";
    echo "🔑 Test accounts:\n";
    echo "   - admin@example.com / password123\n";
    echo "   - test@example.com / password123\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
