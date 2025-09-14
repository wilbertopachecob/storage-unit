<?php

namespace Tests\Unit\Middleware;

use StorageUnit\Tests\TestCase;

class GuardsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->clearSession();
    }

    /**
     * Test isloggedIn function exists
     */
    public function testIsloggedInFunctionExists()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        $this->assertTrue(function_exists('isloggedIn'));
    }

    /**
     * Test isloggedIn returns false when no session
     */
    public function testIsloggedInReturnsFalseWhenNoSession()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        $this->clearSession();
        $this->assertFalse(isloggedIn());
    }

    /**
     * Test isloggedIn returns false when user_id is empty
     */
    public function testIsloggedInReturnsFalseWhenUserIdEmpty()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        $_SESSION['user_id'] = '';
        $this->assertFalse(isloggedIn());
    }

    /**
     * Test isloggedIn returns false when user_id is null
     */
    public function testIsloggedInReturnsFalseWhenUserIdNull()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        $_SESSION['user_id'] = null;
        $this->assertFalse(isloggedIn());
    }

    /**
     * Test isloggedIn returns true when user_id is set
     */
    public function testIsloggedInReturnsTrueWhenUserIdSet()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        $_SESSION['user_id'] = 1;
        $this->assertTrue(isloggedIn());
    }

    /**
     * Test isloggedIn starts session if not started
     */
    public function testIsloggedInStartsSessionIfNotStarted()
    {
        // Clear session completely
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        $this->assertFalse(isloggedIn());
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    /**
     * Test accesingFiles function exists
     */
    public function testAccesingFilesFunctionExists()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        $this->assertTrue(function_exists('accesingFiles'));
    }

    /**
     * Test accesingFiles with different script parameters
     */
    public function testAccesingFilesWithDifferentScripts()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        // Test with itemsList script - should redirect if not logged in
        $_GET['script'] = 'itemsList';
        $this->clearSession();
        
        // Capture output to prevent actual redirect
        ob_start();
        accesingFiles();
        $output = ob_get_clean();
        
        // Should not output anything, but might set headers
        $this->assertEmpty($output);
    }

    /**
     * Test guards file doesn't cause fatal errors
     */
    public function testGuardsFileLoadsWithoutErrors()
    {
        $this->expectNotToPerformAssertions();
        
        // This should not throw any errors
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
    }

    /**
     * Test isloggedIn with different user_id types
     */
    public function testIsloggedInWithDifferentUserIdTypes()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        // Test with string user_id
        $_SESSION['user_id'] = '1';
        $this->assertTrue(isloggedIn());
        
        // Test with integer user_id
        $_SESSION['user_id'] = 1;
        $this->assertTrue(isloggedIn());
        
        // Test with zero (should be false)
        $_SESSION['user_id'] = 0;
        $this->assertFalse(isloggedIn());
        
        // Test with false
        $_SESSION['user_id'] = false;
        $this->assertFalse(isloggedIn());
    }

    /**
     * Test session handling edge cases
     */
    public function testSessionHandlingEdgeCases()
    {
        require_once __DIR__ . '/../../../app/Middleware/guards.php';
        
        // Test with array user_id
        $_SESSION['user_id'] = [1, 2, 3];
        $this->assertTrue(isloggedIn());
        
        // Test with object user_id
        $_SESSION['user_id'] = (object)['id' => 1];
        $this->assertTrue(isloggedIn());
    }
}
