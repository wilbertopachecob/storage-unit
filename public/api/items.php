<?php
/**
 * Items API Endpoint
 * Provides items data for the React frontend
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
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

    // Handle different HTTP methods
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Get all items for the user
            $items = \StorageUnit\Models\Item::getAllWithDetails($user->getId());
            
            echo json_encode([
                'success' => true,
                'data' => $items
            ]);
            break;

        case 'POST':
            // Create new item
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON input'
                ]);
                exit;
            }

            // Validate required fields
            if (empty($input['title'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Title is required'
                ]);
                exit;
            }

            // Create item
            $item = new \StorageUnit\Models\Item(
                $input['title'],
                $input['description'] ?? '',
                $input['qty'] ?? 1,
                $user->getId(),
                $input['img'] ?? null,
                $input['category_id'] ?? null,
                $input['location_id'] ?? null
            );

            if ($item->create()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Item created successfully',
                    'data' => $item
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create item'
                ]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
}
?>
