<?php
/**
 * Autoloader for Storage Unit Management System
 * Implements PSR-4 autoloading standard
 */

spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'StorageUnit\\';
    $base_dir = __DIR__ . '/../app/';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Legacy autoloader for existing classes
spl_autoload_register(function ($class) {
    $legacy_map = [
        'Connection' => __DIR__ . '/../app/Database/Connection.php',
        'User' => __DIR__ . '/../app/Models/User.php',
        'Item' => __DIR__ . '/../app/Models/Item.php',
        'ItemController' => __DIR__ . '/../app/Controllers/ItemController.php',
    ];
    
    if (isset($legacy_map[$class])) {
        require $legacy_map[$class];
    }
});
