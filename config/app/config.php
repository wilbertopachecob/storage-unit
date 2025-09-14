<?php
/**
 * Application Configuration Array
 */

return [
    'app' => [
        'name' => 'Storage Unit Management System',
        'version' => '2.0.0',
        'env' => 'production',
        'debug' => false,
        'url' => 'http://localhost',
    ],
    
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'db',
        'port' => $_ENV['DB_PORT'] ?? 3306,
        'database' => $_ENV['DB_DATABASE'] ?? 'storageunit',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? 'rootpassword',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    
    'security' => [
        'key' => 'your-secret-key-here',
        'session_lifetime' => 120,
        'csrf_token_name' => '_token',
    ],
    
    'upload' => [
        'max_size' => 5242880, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'svg'],
        'path' => 'public/uploads',
    ],
    
    'paths' => [
        'uploads' => 'public/uploads',
        'base_url' => 'http://localhost',
    ],
    
    'logging' => [
        'default' => [
            'type' => 'file',
            'path' => 'storage/logs/app.log',
            'max_file_size' => 10485760, // 10MB
            'max_files' => 5,
        ],
        'database' => [
            'type' => 'file',
            'path' => 'storage/logs/database.log',
            'max_file_size' => 5242880, // 5MB
            'max_files' => 3,
        ],
        'auth' => [
            'type' => 'file',
            'path' => 'storage/logs/auth.log',
            'max_file_size' => 5242880, // 5MB
            'max_files' => 3,
        ],
    ],
];