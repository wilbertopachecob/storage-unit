<?php
/**
 * Analytics Page
 * Serves the React analytics dashboard
 */

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';
require_once __DIR__ . '/../app/Helpers/helpers.php';
require_once __DIR__ . '/../app/Middleware/guards.php';

// Check if user is logged in
if (!isloggedIn()) {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    header("Location: " . $protocol . "://" . $host . "/signin.php");
    exit;
}

// Serve the React app
$indexPath = __DIR__ . '/index.html';
if (!file_exists($indexPath)) {
    die('React app not found. Please run the build script to copy React files to public directory.');
}

// Output the React app
readfile($indexPath);
?>