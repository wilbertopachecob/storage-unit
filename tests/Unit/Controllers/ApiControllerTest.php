<?php
/**
 * API Controller Unit Tests
 */

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use StorageUnit\Controllers\ApiController;
use StorageUnit\Models\User;
use StorageUnit\Core\Database;
use PDO;
use PDOStatement;

class ApiControllerTest extends TestCase
{
    private $mockDb;
    private $mockConnection;
    private $mockStatement;
    private $controller;

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
        
        // Create controller instance
        $this->controller = new class extends ApiController {
            // Test class that extends ApiController
        };
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Database::setInstance(null);
    }

    public function testGetCurrentUserSuccess()
    {
        // Mock user
        $mockUser = $this->createMock(User::class);
        $mockUser->method('getId')->willReturn(1);
        $mockUser->method('getUsername')->willReturn('testuser');
        
        // Mock User::getCurrentUser() static method
        $this->mockStatic(User::class, 'getCurrentUser')
            ->willReturn($mockUser);
        
        $user = $this->controller->getCurrentUser();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->getId());
    }

    public function testGetCurrentUserFails()
    {
        // Mock User::getCurrentUser() to return null
        $this->mockStatic(User::class, 'getCurrentUser')
            ->willReturn(null);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        
        $this->controller->getCurrentUser();
    }

    public function testGetJsonInputValid()
    {
        $inputData = ['name' => 'test', 'value' => 123];
        
        // Mock file_get_contents
        $this->mockFunction('file_get_contents')
            ->with('php://input')
            ->willReturn(json_encode($inputData));
        
        $result = $this->controller->getJsonInput();
        
        $this->assertEquals($inputData, $result);
    }

    public function testGetJsonInputInvalid()
    {
        // Mock file_get_contents to return invalid JSON
        $this->mockFunction('file_get_contents')
            ->with('php://input')
            ->willReturn('invalid json');
        
        $this->expectException(\Exception::class);
        
        $this->controller->getJsonInput();
    }

    public function testValidateRequiredFieldsSuccess()
    {
        $data = ['name' => 'test', 'email' => 'test@example.com'];
        $requiredFields = ['name', 'email'];
        
        // This should not throw an exception
        $this->controller->validateRequiredFields($data, $requiredFields);
        
        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    public function testValidateRequiredFieldsMissing()
    {
        $data = ['name' => 'test'];
        $requiredFields = ['name', 'email'];
        
        $this->expectException(\Exception::class);
        
        $this->controller->validateRequiredFields($data, $requiredFields);
    }

    public function testValidateRequiredFieldsEmpty()
    {
        $data = ['name' => '', 'email' => 'test@example.com'];
        $requiredFields = ['name', 'email'];
        
        $this->expectException(\Exception::class);
        
        $this->controller->validateRequiredFields($data, $requiredFields);
    }

    public function testGetPaginationParams()
    {
        // Test with default values
        $_GET = [];
        $params = $this->controller->getPaginationParams();
        
        $this->assertEquals(1, $params['page']);
        $this->assertEquals(20, $params['limit']);
        $this->assertEquals(0, $params['offset']);
    }

    public function testGetPaginationParamsCustom()
    {
        $_GET = ['page' => 3, 'limit' => 50];
        $params = $this->controller->getPaginationParams();
        
        $this->assertEquals(3, $params['page']);
        $this->assertEquals(50, $params['limit']);
        $this->assertEquals(100, $params['offset']);
    }

    public function testGetPaginationParamsMaxLimit()
    {
        $_GET = ['page' => 1, 'limit' => 200];
        $params = $this->controller->getPaginationParams();
        
        $this->assertEquals(1, $params['page']);
        $this->assertEquals(100, $params['limit']); // Should be capped at 100
        $this->assertEquals(0, $params['offset']);
    }

    public function testGetPaginationParamsMinLimit()
    {
        $_GET = ['page' => 1, 'limit' => 0];
        $params = $this->controller->getPaginationParams();
        
        $this->assertEquals(1, $params['page']);
        $this->assertEquals(1, $params['limit']); // Should be at least 1
        $this->assertEquals(0, $params['offset']);
    }

    public function testGetPaginationParamsMinPage()
    {
        $_GET = ['page' => 0, 'limit' => 20];
        $params = $this->controller->getPaginationParams();
        
        $this->assertEquals(1, $params['page']); // Should be at least 1
        $this->assertEquals(20, $params['limit']);
        $this->assertEquals(0, $params['offset']);
    }

    public function testGetResourceId()
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/items/123';
        
        $id = $this->controller->getResourceId();
        
        $this->assertEquals(123, $id);
    }

    public function testGetResourceIdNotFound()
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/items';
        
        $id = $this->controller->getResourceId();
        
        $this->assertNull($id);
    }

    public function testValidateResourceId()
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/items/123';
        
        $id = $this->controller->validateResourceId();
        
        $this->assertEquals(123, $id);
    }

    public function testValidateResourceIdInvalid()
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/items/invalid';
        
        $this->expectException(\Exception::class);
        
        $this->controller->validateResourceId();
    }

    public function testValidateResourceIdMissing()
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/items';
        
        $this->expectException(\Exception::class);
        
        $this->controller->validateResourceId();
    }

    public function testValidateResourceIdZero()
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/items/0';
        
        $this->expectException(\Exception::class);
        
        $this->controller->validateResourceId();
    }

    public function testSendPaginatedResponse()
    {
        $data = [
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2']
        ];
        $total = 25;
        $page = 2;
        $limit = 10;
        
        // Capture output
        ob_start();
        $this->controller->sendPaginatedResponse($data, $total, $page, $limit);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(200, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals($data, $response['data']['items']);
        
        // Check pagination metadata
        $pagination = $response['data']['pagination'];
        $this->assertEquals(2, $pagination['current_page']);
        $this->assertEquals(10, $pagination['per_page']);
        $this->assertEquals(25, $pagination['total']);
        $this->assertEquals(3, $pagination['total_pages']);
        $this->assertTrue($pagination['has_next']);
        $this->assertTrue($pagination['has_prev']);
    }

    public function testSendPaginatedResponseFirstPage()
    {
        $data = [['id' => 1, 'name' => 'Item 1']];
        $total = 5;
        $page = 1;
        $limit = 10;
        
        ob_start();
        $this->controller->sendPaginatedResponse($data, $total, $page, $limit);
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $pagination = $response['data']['pagination'];
        
        $this->assertFalse($pagination['has_prev']);
        $this->assertFalse($pagination['has_next']);
    }

    public function testSendPaginatedResponseLastPage()
    {
        $data = [['id' => 1, 'name' => 'Item 1']];
        $total = 25;
        $page = 3;
        $limit = 10;
        
        ob_start();
        $this->controller->sendPaginatedResponse($data, $total, $page, $limit);
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $pagination = $response['data']['pagination'];
        
        $this->assertTrue($pagination['has_prev']);
        $this->assertFalse($pagination['has_next']);
    }

    public function testHandleRequestSuccess()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $callbackCalled = false;
        $callback = function() use (&$callbackCalled) {
            $callbackCalled = true;
        };
        
        $this->controller->handleRequest('GET', $callback);
        
        $this->assertTrue($callbackCalled);
    }

    public function testHandleRequestWrongMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $this->expectException(\Exception::class);
        
        $this->controller->handleRequest('GET', function() {});
    }

    /**
     * Mock a static method
     */
    private function mockStatic($className, $methodName)
    {
        // This is a simplified mock - in a real test environment,
        // you would use a proper mocking framework like Mockery
        return $this;
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
