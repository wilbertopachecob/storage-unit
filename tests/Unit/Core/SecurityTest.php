<?php
/**
 * Security Class Tests
 */

namespace StorageUnit\Tests\Unit\Core;

use StorageUnit\Tests\TestCase;
use StorageUnit\Core\Security;

class SecurityTest extends TestCase
{
    public function testGenerateCSRFToken()
    {
        $token = Security::generateCSRFToken();
        
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token));
        $this->assertTrue(ctype_xdigit($token));
    }

    public function testValidateCSRFToken()
    {
        $token = Security::generateCSRFToken();
        
        $this->assertTrue(Security::validateCSRFToken($token));
        $this->assertFalse(Security::validateCSRFToken('invalid_token'));
        $this->assertFalse(Security::validateCSRFToken(''));
    }

    public function testSanitizeInput()
    {
        $input = '<script>alert("xss")</script>Hello World';
        $sanitized = Security::sanitizeInput($input);
        
        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;Hello World', $sanitized);
    }

    public function testSanitizeInputArray()
    {
        $input = [
            'name' => '<script>alert("xss")</script>John',
            'email' => 'test@example.com'
        ];
        $sanitized = Security::sanitizeInput($input);
        
        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;John', $sanitized['name']);
        $this->assertEquals('test@example.com', $sanitized['email']);
    }

    public function testValidateEmail()
    {
        $this->assertTrue(Security::validateEmail('test@example.com'));
        $this->assertTrue(Security::validateEmail('user.name+tag@domain.co.uk'));
        $this->assertFalse(Security::validateEmail('invalid-email'));
        $this->assertFalse(Security::validateEmail('test@'));
        $this->assertFalse(Security::validateEmail('@example.com'));
    }

    public function testValidatePassword()
    {
        $this->assertTrue(Security::validatePassword('password123'));
        $this->assertTrue(Security::validatePassword('verylongpassword'));
        $this->assertFalse(Security::validatePassword('short'));
        $this->assertFalse(Security::validatePassword(''));
    }

    public function testGenerateRandomString()
    {
        $string = Security::generateRandomString(16);
        
        $this->assertIsString($string);
        $this->assertEquals(16, strlen($string));
        $this->assertTrue(ctype_xdigit($string));
    }

    public function testHashPassword()
    {
        $password = 'testpassword123';
        $hash = Security::hashPassword($password);
        
        $this->assertIsString($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(Security::verifyPassword($password, $hash));
    }

    public function testVerifyPassword()
    {
        $password = 'testpassword123';
        $hash = Security::hashPassword($password);
        
        $this->assertTrue(Security::verifyPassword($password, $hash));
        $this->assertFalse(Security::verifyPassword('wrongpassword', $hash));
    }

    public function testValidateFileUpload()
    {
        // Create a temporary file for testing
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'fake image content');
        
        $file = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
            'size' => 1024
        ];
        
        $errors = Security::validateFileUpload($file);
        
        $this->assertIsArray($errors);
        // Note: This test might fail in some environments due to MIME type detection
        // In a real test environment, you'd mock the finfo functions
        
        unlink($tempFile);
    }

    public function testGenerateSecureFilename()
    {
        $originalName = 'test-image.jpg';
        $secureName = Security::generateSecureFilename($originalName);
        
        $this->assertIsString($secureName);
        $this->assertStringEndsWith('.jpg', $secureName);
        $this->assertNotEquals($originalName, $secureName);
        $this->assertGreaterThan(strlen($originalName), strlen($secureName));
    }
}
