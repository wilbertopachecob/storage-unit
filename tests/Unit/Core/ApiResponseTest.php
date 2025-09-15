<?php
/**
 * API Response Unit Tests
 */

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use StorageUnit\Core\ApiResponse;

class ApiResponseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Start output buffering to capture output
        ob_start();
    }

    protected function tearDown(): void
    {
        // Clean up output buffer
        ob_end_clean();
        
        parent::tearDown();
    }

    public function testSuccessResponse()
    {
        $data = ['id' => 1, 'name' => 'Test'];
        $message = 'Success message';
        $statusCode = 200;
        
        // Capture the output
        ob_start();
        ApiResponse::success($data, $message, $statusCode);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals($statusCode, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals($data, $response['data']);
        $this->assertEquals($statusCode, $response['code']);
        $this->assertArrayHasKey('timestamp', $response);
    }

    public function testSuccessResponseWithDefaultValues()
    {
        // Capture the output
        ob_start();
        ApiResponse::success();
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(200, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals('Success', $response['message']);
        $this->assertNull($response['data']);
        $this->assertEquals(200, $response['code']);
    }

    public function testErrorResponse()
    {
        $message = 'Error message';
        $statusCode = 400;
        $error = 'Bad Request';
        $details = ['field' => 'error'];
        
        // Capture the output
        ob_start();
        ApiResponse::error($message, $statusCode, $error, $details);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals($statusCode, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals($error, $response['error']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals($statusCode, $response['code']);
        $this->assertEquals($details, $response['details']);
        $this->assertArrayHasKey('timestamp', $response);
    }

    public function testErrorResponseWithDefaultValues()
    {
        $message = 'Error message';
        
        // Capture the output
        ob_start();
        ApiResponse::error($message);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(400, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Bad Request', $response['error']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals(400, $response['code']);
        $this->assertArrayHasKey('timestamp', $response);
    }

    public function testValidationErrorResponse()
    {
        $errors = ['name' => 'Name is required', 'email' => 'Email is invalid'];
        $message = 'Validation failed';
        
        // Capture the output
        ob_start();
        ApiResponse::validationError($errors, $message);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(422, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Validation Error', $response['error']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals(422, $response['code']);
        $this->assertEquals($errors, $response['details']);
    }

    public function testValidationErrorResponseWithDefaultMessage()
    {
        $errors = ['name' => 'Name is required'];
        
        // Capture the output
        ob_start();
        ApiResponse::validationError($errors);
        $output = ob_get_clean();
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertEquals('Validation failed', $response['message']);
    }

    public function testNotFoundResponse()
    {
        $message = 'Resource not found';
        
        // Capture the output
        ob_start();
        ApiResponse::notFound($message);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(404, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Not Found', $response['error']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals(404, $response['code']);
    }

    public function testNotFoundResponseWithDefaultMessage()
    {
        // Capture the output
        ob_start();
        ApiResponse::notFound();
        $output = ob_get_clean();
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertEquals('Resource not found', $response['message']);
    }

    public function testUnauthorizedResponse()
    {
        $message = 'User not authenticated';
        
        // Capture the output
        ob_start();
        ApiResponse::unauthorized($message);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(401, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Unauthorized', $response['error']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals(401, $response['code']);
    }

    public function testUnauthorizedResponseWithDefaultMessage()
    {
        // Capture the output
        ob_start();
        ApiResponse::unauthorized();
        $output = ob_get_clean();
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertEquals('Unauthorized', $response['message']);
    }

    public function testForbiddenResponse()
    {
        $message = 'Access denied';
        
        // Capture the output
        ob_start();
        ApiResponse::forbidden($message);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(403, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Forbidden', $response['error']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals(403, $response['code']);
    }

    public function testForbiddenResponseWithDefaultMessage()
    {
        // Capture the output
        ob_start();
        ApiResponse::forbidden();
        $output = ob_get_clean();
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertEquals('Forbidden', $response['message']);
    }

    public function testMethodNotAllowedResponse()
    {
        $message = 'POST method not allowed';
        
        // Capture the output
        ob_start();
        ApiResponse::methodNotAllowed($message);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(405, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Method Not Allowed', $response['error']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals(405, $response['code']);
    }

    public function testMethodNotAllowedResponseWithDefaultMessage()
    {
        // Capture the output
        ob_start();
        ApiResponse::methodNotAllowed();
        $output = ob_get_clean();
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertEquals('Method not allowed', $response['message']);
    }

    public function testCreatedResponse()
    {
        $data = ['id' => 1, 'name' => 'New Item'];
        $message = 'Item created successfully';
        
        // Capture the output
        ob_start();
        ApiResponse::created($data, $message);
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(201, http_response_code());
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals($message, $response['message']);
        $this->assertEquals($data, $response['data']);
        $this->assertEquals(201, $response['code']);
    }

    public function testCreatedResponseWithDefaultMessage()
    {
        $data = ['id' => 1];
        
        // Capture the output
        ob_start();
        ApiResponse::created($data);
        $output = ob_get_clean();
        
        // Check JSON output
        $response = json_decode($output, true);
        $this->assertEquals('Resource created successfully', $response['message']);
    }

    public function testNoContentResponse()
    {
        // Capture the output
        ob_start();
        ApiResponse::noContent();
        $output = ob_get_clean();
        
        // Check HTTP status code
        $this->assertEquals(204, http_response_code());
        
        // Check that no output was produced
        $this->assertEmpty($output);
    }

    public function testResponseExits()
    {
        // Test that the response methods exit after output
        $this->expectException(\Exception::class);
        
        // This should not be reached because ApiResponse::success() calls exit()
        ApiResponse::success();
        $this->fail('ApiResponse::success() should have exited');
    }
}
