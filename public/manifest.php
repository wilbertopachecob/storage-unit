<?php
/**
 * Asset Manifest Endpoint
 * Serves the React app asset manifest
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$manifestPath = __DIR__ . '/asset-manifest.json';

if (file_exists($manifestPath)) {
    echo file_get_contents($manifestPath);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Asset manifest not found']);
}
?>