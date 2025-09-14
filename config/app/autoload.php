<?php
/**
 * Autoloader for Storage Unit Management System
 * Implements PSR-4 autoloading standard
 */

// Load constants first
require_once __DIR__ . '/constants.php';

spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'StorageUnit\\';
    $base_dir = dirname(dirname(__DIR__)) . '/app/';
    
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
    // Get the project root directory (where composer.json is located)
    $project_root = dirname(__DIR__);
    
    $legacy_map = [
        'StorageUnit\\Database\\Connection' => $project_root . '/app/Database/Connection.php',
        'StorageUnit\\Models\\User' => $project_root . '/app/Models/User.php',
        'StorageUnit\\Models\\Item' => $project_root . '/app/Models/Item.php',
        'StorageUnit\\Controllers\\ItemController' => $project_root . '/app/Controllers/ItemController.php',
        'StorageUnit\\Core\\LoggerInterface' => $project_root . '/app/Core/LoggerInterface.php',
        'StorageUnit\\Core\\FileLogger' => $project_root . '/app/Core/FileLogger.php',
        'StorageUnit\\Core\\LoggerFactory' => $project_root . '/app/Core/LoggerFactory.php',
    ];
    
    if (isset($legacy_map[$class])) {
        if (file_exists($legacy_map[$class])) {
            require $legacy_map[$class];
        }
    }
});