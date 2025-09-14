<?php
/**
 * User Registration Feature Tests
 * Tests the complete user registration workflow
 */

namespace StorageUnit\Tests\Feature;

use StorageUnit\Tests\TestCase;
use StorageUnit\Controllers\AuthController;

class UserRegistrationTest extends TestCase
{
    public function testCompleteRegistrationWorkflow()
    {
        // Test data
        $userData = [
            'name' => 'Feature Test User',
            'email' => 'featuretest@example.com',
            'password' => 'password123'
        ];

        // Step 1: User is not authenticated initially
        $this->clearSession();
        $this->assertFalse(User::isLoggedIn());

        // Step 2: Submit registration form
        $_POST = array_merge($userData, [
            'csrf_token' => Security::generateCSRFToken()
        ]);

        $authController = new AuthController();
        $result = $authController->register();

        // Step 3: Registration should be successful
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);

        // Step 4: User should be authenticated after registration
        $this->assertTrue(User::isLoggedIn());

        // Step 5: User data should be stored in database
        $currentUser = User::getCurrentUser();
        $this->assertNotNull($currentUser);
        $this->assertEquals($userData['name'], $currentUser->getName());
        $this->assertEquals($userData['email'], $currentUser->getEmail());
    }

    public function testRegistrationWithValidationErrors()
    {
        $this->clearSession();

        // Test with invalid data
        $invalidData = [
            'name' => '', // Empty name
            'email' => 'invalid-email', // Invalid email
            'password' => 'short', // Short password
            'csrf_token' => Security::generateCSRFToken()
        ];

        $_POST = $invalidData;

        $authController = new AuthController();
        $result = $authController->register();

        // Registration should fail
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);

        // User should not be authenticated
        $this->assertFalse(User::isLoggedIn());

        // Should have multiple validation errors
        $this->assertGreaterThan(1, count($result['errors']));
    }

    public function testRegistrationWithDuplicateEmail()
    {
        $this->clearSession();

        // First registration
        $userData = [
            'name' => 'First User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $_POST = $userData;
        $authController = new AuthController();
        $result1 = $authController->register();

        $this->assertTrue($result1['success']);

        // Logout
        User::logout();
        $this->assertFalse(User::isLoggedIn());

        // Second registration with same email
        $userData2 = [
            'name' => 'Second User',
            'email' => 'duplicate@example.com', // Same email
            'password' => 'password456',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $_POST = $userData2;
        $result2 = $authController->register();

        // Second registration should fail
        $this->assertFalse($result2['success']);
        $this->assertNotEmpty($result2['errors']);
        $this->assertStringContainsString('Email already exists', $result2['errors'][0]);

        // User should not be authenticated
        $this->assertFalse(User::isLoggedIn());
    }

    public function testRegistrationWithCSRFProtection()
    {
        $this->clearSession();

        // Test without CSRF token
        $_POST = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
            // No CSRF token
        ];

        $authController = new AuthController();
        $result = $authController->register();

        // Registration should fail due to missing CSRF token
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Invalid security token', $result['errors'][0]);

        // User should not be authenticated
        $this->assertFalse(User::isLoggedIn());
    }

    public function testRegistrationWithInvalidCSRFToken()
    {
        $this->clearSession();

        // Test with invalid CSRF token
        $_POST = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'csrf_token' => 'invalid_token'
        ];

        $authController = new AuthController();
        $result = $authController->register();

        // Registration should fail due to invalid CSRF token
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Invalid security token', $result['errors'][0]);

        // User should not be authenticated
        $this->assertFalse(User::isLoggedIn());
    }

    public function testRegistrationSessionData()
    {
        $this->clearSession();

        $userData = [
            'name' => 'Session Test User',
            'email' => 'sessiontest@example.com',
            'password' => 'password123',
            'csrf_token' => Security::generateCSRFToken()
        ];

        $_POST = $userData;

        $authController = new AuthController();
        $result = $authController->register();

        $this->assertTrue($result['success']);

        // Check session data
        $this->assertArrayHasKey('user_id', $_SESSION);
        $this->assertArrayHasKey('user_name', $_SESSION);
        $this->assertArrayHasKey('user_email', $_SESSION);
        $this->assertEquals($userData['name'], $_SESSION['user_name']);
        $this->assertEquals($userData['email'], $_SESSION['user_email']);
    }
}
