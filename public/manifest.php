<?php
/**
 * Serve manifest.json with proper headers
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$manifestPath = __DIR__ . '/manifest.json';
if (file_exists($manifestPath)) {
    readfile($manifestPath);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Manifest not found']);
}
?>
