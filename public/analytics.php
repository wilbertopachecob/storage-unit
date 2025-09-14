<?php
/**
 * Analytics Page
 * Main analytics dashboard
 */

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';
require_once __DIR__ . '/../app/Middleware/guards.php';

// Check if user is logged in
if (!isloggedIn()) {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    header("Location: " . $protocol . "://" . $host . "/signin.php");
    exit;
}

// Add a comment to test live reload
// This change should be visible immediately without restarting Docker

// Include the analytics dashboard view
include_once __DIR__ . '/../resources/views/analytics/dashboard.php';