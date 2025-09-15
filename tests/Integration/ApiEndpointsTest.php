<?php
/**
 * API Endpoints Integration Tests
 */

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use StorageUnit\Core\Database;
use PDO;
use PDOStatement;

class ApiEndpointsTest extends TestCase
{
    private $mockDb;
    private $mockConnection;
    private $mockStatement;
    private $baseUrl = 'http://localhost:8080/api/v1';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock database connection
        $this->mockStatement = $this->createMock(PDOStatement::class);
        $this->mockConnection = $this->createMock(PDO::class);
        $this->mockDb = $this->createMock(Database::class);
        
        // Set up database mock
        $this->mockDb->method('getConnection')->willReturn($this->mockConnection);
        Database::setInstance($this->mockDb);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Database::setInstance(null);
    }

    public function testItemsApiGetAll()
    {
        $mockData = [
            [
                'id' => 1,
                'title' => 'Test Item',
                'description' => 'Test Description',
                'qty' => 1,
                'category_name' => 'Tools',
                'location_name' => 'Garage'
            ]
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('GET', '/items');
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertArrayHasKey('items', $response['data']['data']);
        $this->assertArrayHasKey('pagination', $response['data']['data']);
    }

    public function testItemsApiGetById()
    {
        $mockData = [
            'id' => 1,
            'title' => 'Test Item',
            'description' => 'Test Description',
            'qty' => 1,
            'user_id' => 1,
            'category_id' => 1,
            'location_id' => 1,
            'img' => 'test.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('GET', '/items/1');
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('Test Item', $response['data']['data']['title']);
    }

    public function testItemsApiCreate()
    {
        $itemData = [
            'title' => 'New Item',
            'description' => 'New Description',
            'qty' => 1,
            'category_id' => 1,
            'location_id' => 1
        ];
        
        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->mockStatement);
            
        $this->mockConnection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(123);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('POST', '/items', $itemData);
        
        $this->assertEquals(201, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('Item created successfully', $response['data']['message']);
    }

    public function testItemsApiUpdate()
    {
        $updateData = [
            'title' => 'Updated Item',
            'description' => 'Updated Description',
            'qty' => 2
        ];
        
        $mockData = [
            'id' => 1,
            'title' => 'Test Item',
            'description' => 'Test Description',
            'qty' => 1,
            'user_id' => 1,
            'category_id' => 1,
            'location_id' => 1,
            'img' => 'test.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('PUT', '/items/1', $updateData);
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('Item updated successfully', $response['data']['message']);
    }

    public function testItemsApiDelete()
    {
        $mockData = [
            'id' => 1,
            'title' => 'Test Item',
            'description' => 'Test Description',
            'qty' => 1,
            'user_id' => 1,
            'category_id' => 1,
            'location_id' => 1,
            'img' => 'test.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('DELETE', '/items/1');
        
        $this->assertEquals(204, $response['code']);
    }

    public function testCategoriesApiGetAll()
    {
        $mockData = [
            [
                'id' => 1,
                'name' => 'Tools',
                'color' => '#ff0000',
                'icon' => 'fas fa-tools',
                'item_count' => 5
            ]
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('GET', '/categories');
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertArrayHasKey('items', $response['data']['data']);
        $this->assertArrayHasKey('pagination', $response['data']['data']);
    }

    public function testCategoriesApiCreate()
    {
        $categoryData = [
            'name' => 'New Category',
            'color' => '#00ff00',
            'icon' => 'fas fa-new'
        ];
        
        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->mockStatement);
            
        $this->mockConnection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(123);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('POST', '/categories', $categoryData);
        
        $this->assertEquals(201, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('Category created successfully', $response['data']['message']);
    }

    public function testLocationsApiGetAll()
    {
        $mockData = [
            [
                'id' => 1,
                'name' => 'Garage',
                'description' => 'Main garage',
                'address' => '123 Main St',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'item_count' => 3
            ]
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('GET', '/locations');
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertArrayHasKey('items', $response['data']['data']);
        $this->assertArrayHasKey('pagination', $response['data']['data']);
    }

    public function testLocationsApiCreate()
    {
        $locationData = [
            'name' => 'New Location',
            'description' => 'New Description',
            'address' => '456 New St',
            'latitude' => 41.8781,
            'longitude' => -87.6298
        ];
        
        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->mockStatement);
            
        $this->mockConnection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(123);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('POST', '/locations', $locationData);
        
        $this->assertEquals(201, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('Location created successfully', $response['data']['message']);
    }

    public function testAnalyticsApiGet()
    {
        $mockData = [
            'total_items' => 25,
            'total_quantity' => 50,
            'total_categories' => 5,
            'total_locations' => 3,
            'image_coverage' => 80.0,
            'avg_quantity' => 2.0
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('GET', '/analytics');
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals(25, $response['data']['data']['total_items']);
    }

    public function testAuthApiLogin()
    {
        $loginData = [
            'username' => 'testuser',
            'password' => 'password123'
        ];
        
        $mockData = [
            'id' => 1,
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Test the API endpoint
        $response = $this->makeApiRequest('POST', '/auth/login', $loginData);
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('Login successful', $response['data']['message']);
    }

    public function testAuthApiRegister()
    {
        $registerData = [
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password' => 'password123',
            'confirm_password' => 'password123'
        ];
        
        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->mockStatement);
            
        $this->mockConnection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(123);
        
        // Test the API endpoint
        $response = $this->makeApiRequest('POST', '/auth/register', $registerData);
        
        $this->assertEquals(201, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('User registered successfully', $response['data']['message']);
    }

    public function testUsersApiGetProfile()
    {
        $mockData = [
            'id' => 1,
            'name' => 'testuser',
            'email' => 'test@example.com',
            'storage_unit_name' => 'My Storage',
            'profile_picture' => 'avatar.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('GET', '/users/profile');
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('testuser', $response['data']['data']['username']);
    }

    public function testUsersApiUpdateProfile()
    {
        $updateData = [
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'storage_unit_name' => 'Updated Storage'
        ];
        
        $mockData = [
            'id' => 1,
            'name' => 'testuser',
            'email' => 'test@example.com',
            'storage_unit_name' => 'My Storage',
            'profile_picture' => 'avatar.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        // Mock user authentication
        $this->mockUserAuthentication();
        
        // Test the API endpoint
        $response = $this->makeApiRequest('PUT', '/users/profile', $updateData);
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
        $this->assertEquals('Profile updated successfully', $response['data']['message']);
    }

    /**
     * Mock user authentication
     */
    private function mockUserAuthentication()
    {
        // Mock session data
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'testuser';
        $_SESSION['authenticated'] = true;
    }

    /**
     * Make an API request
     */
    private function makeApiRequest($method, $endpoint, $data = null)
    {
        // This is a simplified version - in a real test environment,
        // you would use a proper HTTP client like Guzzle
        $url = $this->baseUrl . $endpoint;
        
        // Simulate the request
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $endpoint;
        
        if ($data) {
            $_SERVER['CONTENT_TYPE'] = 'application/json';
            // Simulate JSON input
            $this->mockFunction('file_get_contents')
                ->with('php://input')
                ->willReturn(json_encode($data));
        }
        
        // Return mock response
        return [
            'code' => 200,
            'data' => [
                'success' => true,
                'message' => 'Success',
                'data' => $data
            ]
        ];
    }

    /**
     * Mock a function
     */
    private function mockFunction($functionName)
    {
        // This is a simplified mock - in a real test environment,
        // you would use a proper mocking framework
        return $this;
    }
}
