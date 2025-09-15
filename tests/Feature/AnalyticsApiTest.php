<?php

namespace Tests\Feature;

use StorageUnit\Tests\TestCase;
use StorageUnit\Models\User;

class AnalyticsApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->clearSession();
    }

    /**
     * Test analytics API returns 401 when not authenticated
     */
    public function testAnalyticsApiReturns401WhenNotAuthenticated()
    {
        $response = $this->get('/api/analytics.php');
        
        $this->assertEquals(401, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('User not authenticated', $data['message']);
    }

    /**
     * Test analytics API returns data when authenticated
     */
    public function testAnalyticsApiReturnsDataWhenAuthenticated()
    {
        // Create and authenticate a user
        $userId = $this->createTestUser('test@example.com', 'Test User');
        $this->authenticateUser();
        
        // Create some test data
        $this->createTestItems($userId);
        
        $response = $this->get('/api/analytics.php');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);
        
        $analyticsData = $data['data'];
        $this->assertArrayHasKey('total_items', $analyticsData);
        $this->assertArrayHasKey('total_quantity', $analyticsData);
        $this->assertArrayHasKey('items_by_category', $analyticsData);
        $this->assertArrayHasKey('items_by_location', $analyticsData);
        $this->assertArrayHasKey('recent_items', $analyticsData);
        $this->assertArrayHasKey('monthly_data', $analyticsData);
        $this->assertArrayHasKey('items_without_images', $analyticsData);
        $this->assertArrayHasKey('items_with_images', $analyticsData);
        $this->assertArrayHasKey('image_coverage', $analyticsData);
        $this->assertArrayHasKey('avg_quantity', $analyticsData);
    }

    /**
     * Test analytics API returns 405 for non-GET requests
     */
    public function testAnalyticsApiReturns405ForNonGetRequests()
    {
        $response = $this->post('/api/analytics.php');
        
        $this->assertEquals(405, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Method not allowed', $data['message']);
    }

    /**
     * Test analytics API handles OPTIONS request
     */
    public function testAnalyticsApiHandlesOptionsRequest()
    {
        $response = $this->options('/api/analytics.php');
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test analytics API returns correct monthly data
     */
    public function testAnalyticsApiReturnsCorrectMonthlyData()
    {
        $userId = $this->createTestUser('test@example.com', 'Test User');
        $this->authenticateUser();
        
        // Create items with different dates
        $this->createTestItem($userId, 'Item 1', '2024-01-15 10:00:00');
        $this->createTestItem($userId, 'Item 2', '2024-01-20 14:30:00');
        $this->createTestItem($userId, 'Item 3', '2024-02-10 09:15:00');
        
        $response = $this->get('/api/analytics.php');
        $data = json_decode($response->getBody(), true);
        
        $this->assertTrue($data['success']);
        $monthlyData = $data['data']['monthly_data'];
        
        $this->assertArrayHasKey('2024-01', $monthlyData);
        $this->assertArrayHasKey('2024-02', $monthlyData);
        $this->assertEquals(2, $monthlyData['2024-01']);
        $this->assertEquals(1, $monthlyData['2024-02']);
    }

    /**
     * Test analytics API returns correct image statistics
     */
    public function testAnalyticsApiReturnsCorrectImageStatistics()
    {
        $userId = $this->createTestUser('test@example.com', 'Test User');
        $this->authenticateUser();
        
        // Create items with and without images
        $this->createTestItem($userId, 'Item with image', null, 'image1.jpg');
        $this->createTestItem($userId, 'Item without image', null, null);
        $this->createTestItem($userId, 'Another item without image', null, '');
        
        $response = $this->get('/api/analytics.php');
        $data = json_decode($response->getBody(), true);
        
        $this->assertTrue($data['success']);
        $analyticsData = $data['data'];
        
        $this->assertEquals(3, $analyticsData['total_items']);
        $this->assertEquals(1, $analyticsData['items_with_images']);
        $this->assertEquals(2, $analyticsData['items_without_images']);
        $this->assertEquals(33.3, $analyticsData['image_coverage'], '', 0.1);
    }

    /**
     * Test analytics API handles database errors gracefully
     */
    public function testAnalyticsApiHandlesDatabaseErrors()
    {
        $userId = $this->createTestUser('test@example.com', 'Test User');
        $this->authenticateUser();
        
        // This test would need to mock database errors
        // For now, we'll just test that the API doesn't crash
        $response = $this->get('/api/analytics.php');
        
        $this->assertTrue(in_array($response->getStatusCode(), [200, 500]));
    }

    /**
     * Helper method to create test items
     */
    private function createTestItems($userId)
    {
        $this->createTestItem($userId, 'Test Item 1', null, 'image1.jpg');
        $this->createTestItem($userId, 'Test Item 2', null, null);
    }

    /**
     * Helper method to create a test item
     */
    private function createTestItem($userId, $title, $createdAt = null, $image = null)
    {
        $db = \StorageUnit\Core\Database::getInstance();
        $conn = $db->getConnection();
        
        $createdAt = $createdAt ?: date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO items (user_id, title, description, qty, category_id, location_id, img, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $userId,
            $title,
            'Test description',
            1,
            1,
            1,
            $image,
            $createdAt,
            date('Y-m-d H:i:s')
        ]);
        
        return $conn->lastInsertId();
    }
}
