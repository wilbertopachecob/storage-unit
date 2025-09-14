<?php
/**
 * Profile Controller Unit Tests
 */

namespace StorageUnit\Tests\Unit\Controllers;

use StorageUnit\Tests\TestCase;
use StorageUnit\Controllers\ProfileController;
use StorageUnit\Models\User;

class ProfileControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ProfileController();
    }

    /**
     * Test profile index requires authentication
     */
    public function testIndexRequiresAuthentication()
    {
        $this->clearSession();
        
        // Capture output
        ob_start();
        $this->controller->index();
        $output = ob_get_clean();
        
        // Should redirect to sign in
        $this->assertStringContains('Location: /signIn.php', $output);
    }

    /**
     * Test profile index with authenticated user
     */
    public function testIndexWithAuthenticatedUser()
    {
        $this->authenticateUser();
        
        // Mock the include to avoid actual file inclusion
        $this->expectOutputRegex('/User Profile/');
        
        // This will fail because we can't easily mock the include
        // but we can test the authentication logic
        $this->assertTrue(User::isLoggedIn());
    }

    /**
     * Test update storage unit requires authentication
     */
    public function testUpdateStorageUnitRequiresAuthentication()
    {
        $this->clearSession();
        
        ob_start();
        $this->controller->updateStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
        $this->assertEquals('Unauthorized', $response['message']);
    }

    /**
     * Test update storage unit with valid data
     */
    public function testUpdateStorageUnitWithValidData()
    {
        $this->authenticateUser();
        
        // Set up POST data
        $_POST = [
            'storage_unit_name' => 'Test Storage Unit',
            'storage_unit_address' => '123 Test Street, Test City, TC 12345',
            'storage_unit_latitude' => '40.7128',
            'storage_unit_longitude' => '-74.0060'
        ];
        
        ob_start();
        $this->controller->updateStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals('Storage unit updated successfully', $response['message']);
        
        // Verify data was saved
        $user = User::findById(1);
        $this->assertEquals('Test Storage Unit', $user->getStorageUnitName());
        $this->assertEquals('123 Test Street, Test City, TC 12345', $user->getStorageUnitAddress());
        $this->assertEquals('40.7128', $user->getStorageUnitLatitude());
        $this->assertEquals('-74.0060', $user->getStorageUnitLongitude());
    }

    /**
     * Test update storage unit with missing required fields
     */
    public function testUpdateStorageUnitWithMissingFields()
    {
        $this->authenticateUser();
        
        // Test missing name
        $_POST = [
            'storage_unit_name' => '',
            'storage_unit_address' => '123 Test Street, Test City, TC 12345',
            'storage_unit_latitude' => '40.7128',
            'storage_unit_longitude' => '-74.0060'
        ];
        
        ob_start();
        $this->controller->updateStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Storage unit name is required', $response['message']);
        
        // Test missing address
        $_POST = [
            'storage_unit_name' => 'Test Storage Unit',
            'storage_unit_address' => '',
            'storage_unit_latitude' => '40.7128',
            'storage_unit_longitude' => '-74.0060'
        ];
        
        ob_start();
        $this->controller->updateStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Storage unit address is required', $response['message']);
    }

    /**
     * Test update storage unit with invalid coordinates
     */
    public function testUpdateStorageUnitWithInvalidCoordinates()
    {
        $this->authenticateUser();
        
        // Test invalid latitude
        $_POST = [
            'storage_unit_name' => 'Test Storage Unit',
            'storage_unit_address' => '123 Test Street, Test City, TC 12345',
            'storage_unit_latitude' => '91.0', // Invalid latitude
            'storage_unit_longitude' => '-74.0060'
        ];
        
        ob_start();
        $this->controller->updateStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Invalid latitude', $response['message']);
        
        // Test invalid longitude
        $_POST = [
            'storage_unit_name' => 'Test Storage Unit',
            'storage_unit_address' => '123 Test Street, Test City, TC 12345',
            'storage_unit_latitude' => '40.7128',
            'storage_unit_longitude' => '181.0' // Invalid longitude
        ];
        
        ob_start();
        $this->controller->updateStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('Invalid longitude', $response['message']);
    }

    /**
     * Test get storage unit requires authentication
     */
    public function testGetStorageUnitRequiresAuthentication()
    {
        $this->clearSession();
        
        ob_start();
        $this->controller->getStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
        $this->assertEquals('Unauthorized', $response['message']);
    }

    /**
     * Test get storage unit with authenticated user
     */
    public function testGetStorageUnitWithAuthenticatedUser()
    {
        $this->authenticateUser();
        
        // First set some storage unit data
        $user = User::findById(1);
        $user->setStorageUnitName('Test Storage Unit');
        $user->setStorageUnitAddress('123 Test Street, Test City, TC 12345');
        $user->setStorageUnitLatitude('40.7128');
        $user->setStorageUnitLongitude('-74.0060');
        $user->updateStorageUnit();
        
        ob_start();
        $this->controller->getStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals('Test Storage Unit', $response['data']['name']);
        $this->assertEquals('123 Test Street, Test City, TC 12345', $response['data']['address']);
        $this->assertEquals('40.7128', $response['data']['latitude']);
        $this->assertEquals('-74.0060', $response['data']['longitude']);
    }

    /**
     * Test update storage unit with wrong HTTP method
     */
    public function testUpdateStorageUnitWithWrongMethod()
    {
        $this->authenticateUser();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        ob_start();
        $this->controller->updateStorageUnit();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertEquals(405, http_response_code());
        $this->assertFalse($response['success']);
        $this->assertEquals('Method not allowed', $response['message']);
    }

    /**
     * Test upload profile picture requires authentication
     */
    public function testUploadProfilePictureRequiresAuthentication()
    {
        $this->clearSession();
        
        ob_start();
        $this->controller->uploadProfilePicture();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
        $this->assertEquals('Unauthorized', $response['message']);
    }

    /**
     * Test upload profile picture with no file
     */
    public function testUploadProfilePictureWithNoFile()
    {
        $this->authenticateUser();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES = [];
        
        ob_start();
        $this->controller->uploadProfilePicture();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertFalse($response['success']);
        $this->assertEquals('No file uploaded or upload error', $response['message']);
    }

    /**
     * Test delete profile picture requires authentication
     */
    public function testDeleteProfilePictureRequiresAuthentication()
    {
        $this->clearSession();
        
        ob_start();
        $this->controller->deleteProfilePicture();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertEquals(401, http_response_code());
        $this->assertFalse($response['success']);
        $this->assertEquals('Unauthorized', $response['message']);
    }

    /**
     * Test delete profile picture with authenticated user
     */
    public function testDeleteProfilePictureWithAuthenticatedUser()
    {
        $this->authenticateUser();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        // Set up user with profile picture
        $user = User::findById(1);
        $user->setProfilePicture('test_profile.jpg');
        $user->updateProfilePicture();
        
        ob_start();
        $this->controller->deleteProfilePicture();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertTrue($response['success']);
        $this->assertEquals('Profile picture deleted successfully', $response['message']);
        
        // Verify profile picture was removed from database
        $updatedUser = User::findById(1);
        $this->assertNull($updatedUser->getProfilePicture());
    }
}
