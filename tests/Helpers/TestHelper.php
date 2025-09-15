<?php
/**
 * Test Helper Class
 * Provides common testing utilities
 */

namespace Tests\Helpers;

use PDO;
use PDOStatement;
use StorageUnit\Core\Database;

class TestHelper
{
    /**
     * Create a mock database connection
     */
    public static function createMockDatabase()
    {
        $mockStatement = \PHPUnit\Framework\TestCase::createMock(PDOStatement::class);
        $mockConnection = \PHPUnit\Framework\TestCase::createMock(PDO::class);
        $mockDb = \PHPUnit\Framework\TestCase::createMock(Database::class);
        
        $mockDb->method('getConnection')->willReturn($mockConnection);
        Database::setInstance($mockDb);
        
        return [
            'db' => $mockDb,
            'connection' => $mockConnection,
            'statement' => $mockStatement
        ];
    }

    /**
     * Create a mock user
     */
    public static function createMockUser($id = 1, $username = 'testuser', $email = 'test@example.com')
    {
        $user = \PHPUnit\Framework\TestCase::createMock(\StorageUnit\Models\User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getUsername')->willReturn($username);
        $user->method('getEmail')->willReturn($email);
        $user->method('getName')->willReturn($username);
        $user->method('getStorageUnitName')->willReturn('Test Storage');
        $user->method('getProfilePicture')->willReturn('avatar.jpg');
        $user->method('getCreatedAt')->willReturn('2024-01-01 00:00:00');
        $user->method('getUpdatedAt')->willReturn('2024-01-01 00:00:00');
        
        return $user;
    }

    /**
     * Create mock item data
     */
    public static function createMockItemData($id = 1, $title = 'Test Item')
    {
        return [
            'id' => $id,
            'title' => $title,
            'description' => 'Test Description',
            'qty' => 1,
            'user_id' => 1,
            'category_id' => 1,
            'location_id' => 1,
            'img' => 'test.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
    }

    /**
     * Create mock category data
     */
    public static function createMockCategoryData($id = 1, $name = 'Test Category')
    {
        return [
            'id' => $id,
            'name' => $name,
            'color' => '#ff0000',
            'icon' => 'fas fa-test',
            'user_id' => 1,
            'item_count' => 5,
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
    }

    /**
     * Create mock location data
     */
    public static function createMockLocationData($id = 1, $name = 'Test Location')
    {
        return [
            'id' => $id,
            'name' => $name,
            'description' => 'Test Description',
            'address' => '123 Test St',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'parent_id' => null,
            'user_id' => 1,
            'item_count' => 3,
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
    }

    /**
     * Create mock analytics data
     */
    public static function createMockAnalyticsData()
    {
        return [
            'total_items' => 25,
            'total_quantity' => 50,
            'total_categories' => 5,
            'total_locations' => 3,
            'image_coverage' => 80.0,
            'avg_quantity' => 2.0,
            'monthly_data' => [
                '2024-01' => 5,
                '2024-02' => 8,
                '2024-03' => 12
            ],
            'recent_items' => [
                [
                    'id' => 1,
                    'title' => 'Recent Item',
                    'created_at' => '2024-03-01 00:00:00'
                ]
            ]
        ];
    }

    /**
     * Mock session data
     */
    public static function mockSession($userId = 1, $username = 'testuser')
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['authenticated'] = true;
    }

    /**
     * Clear session data
     */
    public static function clearSession()
    {
        $_SESSION = [];
    }

    /**
     * Mock HTTP request
     */
    public static function mockHttpRequest($method = 'GET', $uri = '/api/v1/items', $data = null)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        
        if ($data) {
            $_SERVER['CONTENT_TYPE'] = 'application/json';
            // Mock file_get_contents for JSON input
            \PHPUnit\Framework\TestCase::createMock('file_get_contents')
                ->method('file_get_contents')
                ->with('php://input')
                ->willReturn(json_encode($data));
        }
    }

    /**
     * Create mock API response
     */
    public static function createMockApiResponse($success = true, $data = null, $message = 'Success', $code = 200)
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'code' => $code,
            'timestamp' => date('c')
        ];
    }

    /**
     * Create mock paginated response
     */
    public static function createMockPaginatedResponse($items, $total, $page = 1, $limit = 20)
    {
        $totalPages = ceil($total / $limit);
        
        return [
            'success' => true,
            'message' => 'Success',
            'data' => [
                'items' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => $totalPages,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ]
            ],
            'code' => 200,
            'timestamp' => date('c')
        ];
    }

    /**
     * Assert API response structure
     */
    public static function assertApiResponse($response, $expectedSuccess = true, $expectedCode = 200)
    {
        \PHPUnit\Framework\TestCase::assertArrayHasKey('success', $response);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('message', $response);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('code', $response);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('timestamp', $response);
        
        \PHPUnit\Framework\TestCase::assertEquals($expectedSuccess, $response['success']);
        \PHPUnit\Framework\TestCase::assertEquals($expectedCode, $response['code']);
    }

    /**
     * Assert paginated response structure
     */
    public static function assertPaginatedResponse($response, $expectedItemCount = null)
    {
        self::assertApiResponse($response);
        
        \PHPUnit\Framework\TestCase::assertArrayHasKey('data', $response);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('items', $response['data']);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('pagination', $response['data']);
        
        if ($expectedItemCount !== null) {
            \PHPUnit\Framework\TestCase::assertCount($expectedItemCount, $response['data']['items']);
        }
        
        $pagination = $response['data']['pagination'];
        \PHPUnit\Framework\TestCase::assertArrayHasKey('current_page', $pagination);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('per_page', $pagination);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('total', $pagination);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('total_pages', $pagination);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('has_next', $pagination);
        \PHPUnit\Framework\TestCase::assertArrayHasKey('has_prev', $pagination);
    }

    /**
     * Create test database schema
     */
    public static function createTestSchema()
    {
        // This would create test database tables
        // In a real implementation, you'd use database migrations
        return true;
    }

    /**
     * Clean up test data
     */
    public static function cleanupTestData()
    {
        // This would clean up test data
        // In a real implementation, you'd truncate test tables
        return true;
    }
}
