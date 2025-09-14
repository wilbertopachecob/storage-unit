<?php
/**
 * Application Configuration
 * Centralized configuration for the Storage Unit Management System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define application constants
define('APP_NAME', 'Storage Unit Management System');
define('APP_VERSION', '2.0.0');
define('APP_DEBUG', true);

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'storageunit');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', 'utf8mb4');

// File upload configuration
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024); // 2MB
define('UPLOAD_ALLOWED_TYPES', ['jpeg', 'jpg', 'png']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Security configuration
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour
define('SESSION_LIFETIME', 7200); // 2 hours
define('PASSWORD_MIN_LENGTH', 8);

// Application paths
define('APP_ROOT', dirname(__DIR__));
define('SRC_PATH', APP_ROOT . '/src');
define('VIEWS_PATH', APP_ROOT . '/views');
define('PUBLIC_PATH', APP_ROOT . '/public');
define('UPLOADS_PATH', APP_ROOT . '/uploads');

// URL configuration
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));
define('ASSETS_URL', BASE_URL . '/public');

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('UTC');

// Include autoloader
require_once __DIR__ . '/autoload.php';
