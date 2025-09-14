<?php
/**
 * Analytics API Endpoint
 * Provides analytics data for the React frontend
 */

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
    // Check if user is authenticated
    $user = \StorageUnit\Models\User::getCurrentUser();
    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'User not authenticated'
        ]);
        exit;
    }

    // Only allow GET requests
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        exit;
    }

    // Get analytics data
    $controller = new \StorageUnit\Controllers\EnhancedItemController();
    $analytics = $controller->analytics();

    // Get additional data for enhanced analytics
    $items = \StorageUnit\Models\Item::getAllWithDetails($user->getId());
    $categories = \StorageUnit\Models\Category::getWithItemCount($user->getId());
    $locations = \StorageUnit\Models\Location::getWithItemCount($user->getId());

    // Calculate additional statistics
    $itemsWithoutImages = array_filter($items, function($item) {
        return empty($item['img']);
    });

    // Group items by month for chart data
    $monthlyData = [];
    foreach ($items as $item) {
        $month = date('Y-m', strtotime($item['created_at']));
        if (!isset($monthlyData[$month])) {
            $monthlyData[$month] = 0;
        }
        $monthlyData[$month]++;
    }
    ksort($monthlyData);

    // Prepare response data
    $response = [
        'success' => true,
        'data' => [
            'total_items' => $analytics['total_items'],
            'total_quantity' => $analytics['total_quantity'],
            'items_by_category' => $analytics['items_by_category'],
            'items_by_location' => $analytics['items_by_location'],
            'recent_items' => array_slice($items, 0, 5),
            'monthly_data' => $monthlyData,
            'items_without_images' => count($itemsWithoutImages),
            'items_with_images' => count($items) - count($itemsWithoutImages),
            'image_coverage' => count($items) > 0 ? round((count($items) - count($itemsWithoutImages)) / count($items) * 100, 1) : 0,
            'avg_quantity' => $analytics['total_items'] > 0 ? round($analytics['total_quantity'] / $analytics['total_items'], 1) : 0
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
?>
