<?php
/**
 * API v1 Router
 * Handles routing for version 1 of the API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session and include necessary files
session_start();
include_once __DIR__ . '/../../../app/Helpers/helpers.php';
include_once __DIR__ . '/../../../app/Middleware/guards.php';
include_once __DIR__ . '/../../../config/app/autoload.php';

// Get the request path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Remove 'api/v1' from the path
$apiPath = array_slice($pathParts, 2); // Remove 'api' and 'v1'

// Route to appropriate endpoint
$resource = $apiPath[0] ?? '';
$id = $apiPath[1] ?? null;

try {
    switch ($resource) {
        case 'items':
            include __DIR__ . '/items.php';
            break;
        case 'categories':
            include __DIR__ . '/categories.php';
            break;
        case 'locations':
            include __DIR__ . '/locations.php';
            break;
        case 'analytics':
            include __DIR__ . '/analytics.php';
            break;
        case 'users':
            include __DIR__ . '/users.php';
            break;
        case 'auth':
            include __DIR__ . '/auth.php';
            break;
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Not Found',
                'message' => 'API endpoint not found',
                'code' => 404
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal Server Error',
        'message' => 'An unexpected error occurred',
        'code' => 500
    ]);
}
?>
