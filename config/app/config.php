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
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'storage_unit',
        'username' => 'root',
        'password' => '',
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
];