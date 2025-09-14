<?php
/**
 * Analytics Page without Authentication Check
 * For debugging React app issues
 */

// Skip authentication for debugging
// session_start();
// require_once __DIR__ . '/../vendor/autoload.php';
// require_once __DIR__ . '/../config/app/config.php';
// require_once __DIR__ . '/../config/app/constants.php';
// require_once __DIR__ . '/../app/Helpers/helpers.php';
// require_once __DIR__ . '/../app/Middleware/guards.php';

// Check if user is logged in
// if (!isloggedIn()) {
//     $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
//     $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
//     header("Location: " . $protocol . "://" . $host . "/signin.php");
//     exit;
// }

// Read and modify the React app HTML
$indexPath = __DIR__ . '/index.html';
if (!file_exists($indexPath)) {
    die('React app not found. Please run the build script to copy React files to public directory.');
}

$html = file_get_contents($indexPath);

// Fix manifest.json reference
$html = str_replace('href="/manifest.json"', 'href="/manifest.php"', $html);

// Add debugging script
$html = str_replace('</body>', '
<script>
console.log("Analytics page loaded without auth check");
console.log("API URL:", window.location.origin + "/api");

// Test API call
fetch("/api/analytics.php")
    .then(response => {
        console.log("Analytics API response:", response.status);
        return response.text();
    })
    .then(data => {
        console.log("Analytics API data:", data);
    })
    .catch(error => {
        console.error("Analytics API error:", error);
    });
</script>
</body>', $html);

echo $html;
