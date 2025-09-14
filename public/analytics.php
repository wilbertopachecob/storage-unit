<?php
/**
 * Analytics Page
 * Main analytics dashboard
 */

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';

// Check if user is logged in
if (!isloggedIn()) {
    header('Location: /signin.php');
    exit;
}

// Include the analytics dashboard view
include_once __DIR__ . '/../resources/views/analytics/dashboard.php';