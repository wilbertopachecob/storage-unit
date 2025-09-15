<?php
/**
 * API Test Script
 * Simple script to test the new API endpoints
 */

// Set the base URL for your API
$baseUrl = 'http://localhost:8080/api/v1';

// Test data
$testUser = [
    'username' => 'testuser_' . time(),
    'email' => 'test_' . time() . '@example.com',
    'password' => 'testpassword123',
    'confirm_password' => 'testpassword123'
];

$testItem = [
    'title' => 'Test Item ' . time(),
    'description' => 'This is a test item',
    'qty' => 1
];

$testCategory = [
    'name' => 'Test Category ' . time(),
    'color' => '#ff0000',
    'icon' => 'fas fa-test'
];

$testLocation = [
    'name' => 'Test Location ' . time(),
    'description' => 'This is a test location',
    'address' => '123 Test Street'
];

// Function to make API requests
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

echo "=== Storage Unit API Test ===\n\n";

// Test 1: Register user
echo "1. Testing user registration...\n";
$response = makeRequest($baseUrl . '/auth/register', 'POST', $testUser);
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 201) {
    echo "❌ User registration failed!\n";
    exit;
}

echo "✅ User registration successful!\n\n";

// Test 2: Login user
echo "2. Testing user login...\n";
$loginData = [
    'username' => $testUser['username'],
    'password' => $testUser['password']
];
$response = makeRequest($baseUrl . '/auth/login', 'POST', $loginData);
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 200) {
    echo "❌ User login failed!\n";
    exit;
}

echo "✅ User login successful!\n\n";

// Test 3: Get user profile
echo "3. Testing get user profile...\n";
$response = makeRequest($baseUrl . '/auth/me', 'GET');
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 200) {
    echo "❌ Get user profile failed!\n";
    exit;
}

echo "✅ Get user profile successful!\n\n";

// Test 4: Create category
echo "4. Testing create category...\n";
$response = makeRequest($baseUrl . '/categories', 'POST', $testCategory);
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 201) {
    echo "❌ Create category failed!\n";
    exit;
}

$categoryId = $response['data']['data']['id'];
echo "✅ Create category successful! Category ID: $categoryId\n\n";

// Test 5: Create location
echo "5. Testing create location...\n";
$response = makeRequest($baseUrl . '/locations', 'POST', $testLocation);
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 201) {
    echo "❌ Create location failed!\n";
    exit;
}

$locationId = $response['data']['data']['id'];
echo "✅ Create location successful! Location ID: $locationId\n\n";

// Test 6: Create item
echo "6. Testing create item...\n";
$testItem['category_id'] = $categoryId;
$testItem['location_id'] = $locationId;
$response = makeRequest($baseUrl . '/items', 'POST', $testItem);
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 201) {
    echo "❌ Create item failed!\n";
    exit;
}

$itemId = $response['data']['data']['id'];
echo "✅ Create item successful! Item ID: $itemId\n\n";

// Test 7: Get all items
echo "7. Testing get all items...\n";
$response = makeRequest($baseUrl . '/items', 'GET');
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 200) {
    echo "❌ Get all items failed!\n";
    exit;
}

echo "✅ Get all items successful!\n\n";

// Test 8: Get analytics
echo "8. Testing get analytics...\n";
$response = makeRequest($baseUrl . '/analytics', 'GET');
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 200) {
    echo "❌ Get analytics failed!\n";
    exit;
}

echo "✅ Get analytics successful!\n\n";

// Test 9: Update item
echo "9. Testing update item...\n";
$updateData = [
    'title' => 'Updated Test Item',
    'description' => 'This item has been updated',
    'qty' => 2
];
$response = makeRequest($baseUrl . '/items/' . $itemId, 'PUT', $updateData);
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 200) {
    echo "❌ Update item failed!\n";
    exit;
}

echo "✅ Update item successful!\n\n";

// Test 10: Delete item
echo "10. Testing delete item...\n";
$response = makeRequest($baseUrl . '/items/' . $itemId, 'DELETE');
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 204) {
    echo "❌ Delete item failed!\n";
    exit;
}

echo "✅ Delete item successful!\n\n";

// Test 11: Logout
echo "11. Testing logout...\n";
$response = makeRequest($baseUrl . '/auth/logout', 'POST');
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['code'] !== 200) {
    echo "❌ Logout failed!\n";
    exit;
}

echo "✅ Logout successful!\n\n";

echo "=== All API tests completed successfully! ===\n";
echo "The new RESTful API is working correctly.\n";
?>
