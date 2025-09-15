<?php
/**
 * Analytics API Endpoint
 * Provides analytics data for the React frontend using the AnalyticsController
 */

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session and include necessary files
session_start();
include_once __DIR__ . '/../../app/Helpers/helpers.php';
include_once __DIR__ . '/../../app/Middleware/guards.php';
include_once __DIR__ . '/../../config/app/autoload.php';

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        exit;
    }

    // Initialize analytics controller
    $analyticsController = new \StorageUnit\Controllers\AnalyticsController();
    
    // Validate user authentication
    $user = $analyticsController->validateUser();
    
    // Get analytics data
    $response = $analyticsController->getApiResponse($user->getId());
    
    // Set appropriate HTTP status code
    if (!$response['success']) {
        http_response_code(500);
    }
    
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
?>
