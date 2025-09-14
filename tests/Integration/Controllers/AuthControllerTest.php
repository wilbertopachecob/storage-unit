<?php
/**
 * Authentication Controller Integration Tests
 */

namespace StorageUnit\Tests\Integration\Controllers;

use StorageUnit\Tests\TestCase;
use StorageUnit\Controllers\AuthController;

class AuthControllerTest extends TestCase
{
    private $authController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authController = new AuthController();
    }

    public function testUserRegistration()
    {
        $_POST = [
            'name' => 'New Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->authController->register();

        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertTrue($this->authController->isAuthenticated());
    }

    public function testUserRegistrationWithInvalidEmail()
    {
        $_POST = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->authController->register();

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Valid email is required', $result['errors'][0]);
    }

    public function testUserRegistrationWithShortPassword()
    {
        $_POST = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->authController->register();

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Password must be at least', $result['errors'][0]);
    }

    public function testUserRegistrationWithDuplicateEmail()
    {
        // First registration
        $_POST = [
            'name' => 'First User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'csrf_token' => Security::generateCSRFToken()
        ];
        $this->authController->register();

        // Second registration with same email
        $_POST = [
            'name' => 'Second User',
            'email' => 'duplicate@example.com',
            'password' => 'password456',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->authController->register();

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Email already exists', $result['errors'][0]);
    }

    public function testUserLogin()
    {
        // Create a test user first
        $this->createTestUser('logintest@example.com', 'Login Test User');

        $_POST = [
            'email' => 'logintest@example.com',
            'password' => 'password123',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->authController->login();

        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertTrue($this->authController->isAuthenticated());
    }

    public function testUserLoginWithWrongPassword()
    {
        $this->createTestUser('logintest2@example.com', 'Login Test User 2');

        $_POST = [
            'email' => 'logintest2@example.com',
            'password' => 'wrongpassword',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->authController->login();

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Invalid email or password', $result['errors'][0]);
    }

    public function testUserLoginWithNonExistentEmail()
    {
        $_POST = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->authController->login();

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Invalid email or password', $result['errors'][0]);
    }

    public function testInvalidCSRFToken()
    {
        $_POST = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'csrf_token' => 'invalid_token'
        ];

        $result = $this->authController->register();

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Invalid security token', $result['errors'][0]);
    }

    public function testGetCurrentUser()
    {
        $this->authenticateUser();
        
        $currentUser = $this->authController->getCurrentUser();
        
        $this->assertNotNull($currentUser);
        $this->assertEquals(1, $currentUser->getId());
        $this->assertEquals('Test User', $currentUser->getName());
    }

    public function testGetCurrentUserWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $currentUser = $this->authController->getCurrentUser();
        
        $this->assertNull($currentUser);
    }

    public function testIsAuthenticated()
    {
        $this->clearSession();
        $this->assertFalse($this->authController->isAuthenticated());
        
        $this->authenticateUser();
        $this->assertTrue($this->authController->isAuthenticated());
    }
}
