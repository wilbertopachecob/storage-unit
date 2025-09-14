<?php

namespace Tests\Feature;

use StorageUnit\Tests\TestCase;
use StorageUnit\Models\User;

class AnalyticsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->clearSession();
    }

    /**
     * Test that analytics page redirects to signin when not logged in
     */
    public function testAnalyticsRedirectsWhenNotLoggedIn()
    {
        $response = $this->get('/analytics.php');
        
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('signin.php', $response->getHeader('Location')[0] ?? '');
    }

    /**
     * Test that analytics page loads when user is logged in
     */
    public function testAnalyticsLoadsWhenLoggedIn()
    {
        // Create and authenticate a user
        $this->createTestUser('test@example.com', 'Test User');
        $this->authenticateUser();
        
        $response = $this->get('/analytics.php');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Analytics', $response->getBody());
    }

    /**
     * Test that isloggedIn function is available
     */
    public function testIsloggedInFunctionExists()
    {
        // Include the guards file
        require_once __DIR__ . '/../../app/Middleware/guards.php';
        
        $this->assertTrue(function_exists('isloggedIn'));
    }

    /**
     * Test that isloggedIn returns false when not logged in
     */
    public function testIsloggedInReturnsFalseWhenNotLoggedIn()
    {
        require_once __DIR__ . '/../../app/Middleware/guards.php';
        
        $this->clearSession();
        $this->assertFalse(isloggedIn());
    }

    /**
     * Test that isloggedIn returns true when logged in
     */
    public function testIsloggedInReturnsTrueWhenLoggedIn()
    {
        require_once __DIR__ . '/../../app/Middleware/guards.php';
        
        $this->createTestUser('test@example.com', 'Test User');
        $this->authenticateUser();
        
        $this->assertTrue(isloggedIn());
    }

    /**
     * Test analytics page includes dashboard view
     */
    public function testAnalyticsIncludesDashboardView()
    {
        $this->createTestUser('test@example.com', 'Test User');
        $this->authenticateUser();
        
        $response = $this->get('/analytics.php');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('dashboard', $response->getBody());
    }

    /**
     * Test that analytics page has proper session handling
     */
    public function testAnalyticsSessionHandling()
    {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Test without user_id
        $_SESSION = [];
        require_once __DIR__ . '/../../app/Middleware/guards.php';
        $this->assertFalse(isloggedIn());
        
        // Test with user_id
        $_SESSION['user_id'] = 1;
        $this->assertTrue(isloggedIn());
    }

    /**
     * Test analytics page security - prevents direct access without authentication
     */
    public function testAnalyticsSecurity()
    {
        // Clear any existing session
        $this->clearSession();
        
        $response = $this->get('/analytics.php');
        
        // Should redirect to signin
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('signin.php', $response->getHeader('Location')[0] ?? '');
    }

    /**
     * Test that analytics page loads with proper headers
     */
    public function testAnalyticsHeaders()
    {
        $this->createTestUser('test@example.com', 'Test User');
        $this->authenticateUser();
        
        $response = $this->get('/analytics.php');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeader('Content-Type')[0] ?? '');
    }

    /**
     * Test analytics page with different user sessions
     */
    public function testAnalyticsWithDifferentUsers()
    {
        // Create two users
        $user1Id = $this->createTestUser('user1@example.com', 'User 1');
        $user2Id = $this->createTestUser('user2@example.com', 'User 2');
        
        // Test with user 1
        $_SESSION['user_id'] = $user1Id;
        require_once __DIR__ . '/../../app/Middleware/guards.php';
        $this->assertTrue(isloggedIn());
        
        // Test with user 2
        $_SESSION['user_id'] = $user2Id;
        $this->assertTrue(isloggedIn());
        
        // Test with invalid user
        $_SESSION['user_id'] = 999;
        $this->assertTrue(isloggedIn()); // Function only checks if user_id exists and is not empty
    }

    /**
     * Test analytics page error handling
     */
    public function testAnalyticsErrorHandling()
    {
        // Test with malformed session data
        $_SESSION['user_id'] = '';
        require_once __DIR__ . '/../../app/Middleware/guards.php';
        $this->assertFalse(isloggedIn());
        
        // Test with null user_id
        $_SESSION['user_id'] = null;
        $this->assertFalse(isloggedIn());
    }
}
