<?php
/**
 * Export Items by Location
 * Downloads items filtered by location as CSV
 */

// Start session and include necessary files
session_start();
require_once __DIR__ . '/../../config/app/autoload.php';
require_once __DIR__ . '/../../app/Middleware/guards.php';

// Check if user is logged in
if (!isloggedIn()) {
    header('Location: /signIn.php');
    exit;
}

// Get location ID from URL parameter
$locationId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$locationId) {
    header('HTTP/1.1 400 Bad Request');
    echo "Location ID is required.";
    exit;
}

try {
    $controller = new \StorageUnit\Controllers\ExportController();
    $controller->exportItemsByLocation($locationId);
} catch (Exception $e) {
    error_log("Export error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo "Export failed. Please try again.";
    exit;
}
