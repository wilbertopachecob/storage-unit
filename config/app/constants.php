<?php
/**
 * Application Constants
 * Defines constants for backward compatibility
 */

// Load configuration array
$config = require __DIR__ . '/config.php';

// Define constants for backward compatibility
define('APP_NAME', $config['app']['name']);
define('APP_VERSION', $config['app']['version']);
define('APP_ENV', $config['app']['env']);
define('APP_DEBUG', $config['app']['debug']);
define('APP_URL', $config['app']['url']);

define('DB_HOST', $config['database']['host']);
define('DB_PORT', $config['database']['port']);
// Use test database for testing environment
define('DB_DATABASE', $_ENV['APP_ENV'] === 'testing' ? 'storageunit_test' : $config['database']['database']);
define('DB_USERNAME', $config['database']['username']);
define('DB_PASSWORD', $config['database']['password']);
define('DB_CHARSET', $config['database']['charset']);

define('UPLOADS_PATH', $config['upload']['path']);
define('UPLOAD_MAX_SIZE', $config['upload']['max_size']);
define('UPLOAD_ALLOWED_TYPES', implode(',', $config['upload']['allowed_types']));

define('BASE_URL', $config['paths']['base_url']);

// Security constants
define('APP_KEY', $config['security']['key']);
define('SESSION_LIFETIME', $config['security']['session_lifetime']);
define('CSRF_TOKEN_NAME', $config['security']['csrf_token_name']);
define('PASSWORD_MIN_LENGTH', 8);
