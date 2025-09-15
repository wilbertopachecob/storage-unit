<?php

namespace Tests\Unit\Controllers;

use StorageUnit\Tests\TestCase;
use StorageUnit\Controllers\AnalyticsController;
use StorageUnit\Models\User;
use StorageUnit\Models\Item;
use StorageUnit\Models\Category;
use StorageUnit\Models\Location;

class AnalyticsControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new AnalyticsController();
        $this->clearSession();
    }

    /**
     * Test getAnalyticsData returns correct structure
     */
    public function testGetAnalyticsDataReturnsCorrectStructure()
    {
        // Create test user and items
        $userId = $this->createTestUser('test@example.com', 'Test User');
        $this->createTestItems($userId);
        
        $result = $this->controller->getAnalyticsData($userId);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertTrue($result['success']);
        
        $data = $result['data'];
        $this->assertArrayHasKey('total_items', $data);
        $this->assertArrayHasKey('total_quantity', $data);
        $this->assertArrayHasKey('items_by_category', $data);
        $this->assertArrayHasKey('items_by_location', $data);
        $this->assertArrayHasKey('recent_items', $data);
        $this->assertArrayHasKey('monthly_data', $data);
        $this->assertArrayHasKey('items_without_images', $data);
        $this->assertArrayHasKey('items_with_images', $data);
        $this->assertArrayHasKey('image_coverage', $data);
        $this->assertArrayHasKey('avg_quantity', $data);
    }

    /**
     * Test getAnalyticsData throws exception for non-existent user
     */
    public function testGetAnalyticsDataThrowsExceptionForNonExistentUser()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');
        
        $this->controller->getAnalyticsData(999);
    }

    /**
     * Test getApiResponse returns success response
     */
    public function testGetApiResponseReturnsSuccessResponse()
    {
        $userId = $this->createTestUser('test@example.com', 'Test User');
        $this->createTestItems($userId);
        
        $result = $this->controller->getApiResponse($userId);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    /**
     * Test getApiResponse handles exceptions gracefully
     */
    public function testGetApiResponseHandlesExceptions()
    {
        $result = $this->controller->getApiResponse(999);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('User not found', $result['message']);
    }

    /**
     * Test validateUser returns user when authenticated
     */
    public function testValidateUserReturnsUserWhenAuthenticated()
    {
        $userId = $this->createTestUser('test@example.com', 'Test User');
        $_SESSION['user_id'] = $userId;
        
        $user = $this->controller->validateUser();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->getId());
    }

    /**
     * Test validateUser throws exception when not authenticated
     */
    public function testValidateUserThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        
        $this->controller->validateUser();
    }

    /**
     * Test monthly data calculation
     */
    public function testMonthlyDataCalculation()
    {
        $userId = $this->createTestUser('test@example.com', 'Test User');
        
        // Create items with different dates
        $this->createTestItem($userId, 'Item 1', '2024-01-15 10:00:00');
        $this->createTestItem($userId, 'Item 2', '2024-01-20 14:30:00');
        $this->createTestItem($userId, 'Item 3', '2024-02-10 09:15:00');
        
        $result = $this->controller->getAnalyticsData($userId);
        $monthlyData = $result['data']['monthly_data'];
        
        $this->assertArrayHasKey('2024-01', $monthlyData);
        $this->assertArrayHasKey('2024-02', $monthlyData);
        $this->assertEquals(2, $monthlyData['2024-01']);
        $this->assertEquals(1, $monthlyData['2024-02']);
    }

    /**
     * Test image statistics calculation
     */
    public function testImageStatisticsCalculation()
    {
        $userId = $this->createTestUser('test@example.com', 'Test User');
        
        // Create items with and without images
        $this->createTestItem($userId, 'Item with image', null, 'image1.jpg');
        $this->createTestItem($userId, 'Item without image', null, null);
        $this->createTestItem($userId, 'Another item without image', null, '');
        
        $result = $this->controller->getAnalyticsData($userId);
        $data = $result['data'];
        
        $this->assertEquals(3, $data['total_items']);
        $this->assertEquals(1, $data['items_with_images']);
        $this->assertEquals(2, $data['items_without_images']);
        $this->assertEquals(33.3, $data['image_coverage'], '', 0.1);
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
