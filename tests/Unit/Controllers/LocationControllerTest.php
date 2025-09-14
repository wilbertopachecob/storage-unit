<?php
/**
 * Location Controller Tests
 */

namespace StorageUnit\Tests\Unit\Controllers;

use StorageUnit\Tests\TestCase;
use StorageUnit\Controllers\LocationController;
use StorageUnit\Models\Location;

class LocationControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new LocationController();
        $this->authenticateUser(1);
    }

    public function testIndexReturnsLocationsWithItemCount()
    {
        $result = $this->controller->index();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('locations', $result);
        $this->assertArrayHasKey('hierarchy', $result);
        $this->assertArrayHasKey('total_count', $result);
        
        $this->assertCount(3, $result['locations']); // Should have 3 locations for user 1
        $this->assertEquals(3, $result['total_count']);
        
        // Check that locations have item_count
        foreach ($result['locations'] as $location) {
            $this->assertArrayHasKey('item_count', $location);
        }
        
        // Check hierarchy structure
        $this->assertIsArray($result['hierarchy']);
        $this->assertCount(2, $result['hierarchy']); // Should have 2 root locations
    }

    public function testIndexThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->index();
    }

    public function testShowReturnsLocation()
    {
        $result = $this->controller->show(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('location', $result);
        
        $this->assertInstanceOf(Location::class, $result['location']);
        $this->assertEquals(1, $result['location']->getId());
        $this->assertEquals('Garage', $result['location']->getName());
    }

    public function testShowThrowsExceptionWhenLocationNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Location not found');
        $this->controller->show(999);
    }

    public function testShowThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->show(1);
    }

    public function testCreateWithValidData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'New Location';
        $_POST['parent_id'] = '';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('message', $result);
        
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertEquals('Location created successfully', $result['message']);
    }

    public function testCreateWithParentLocation()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Child Location';
        $_POST['parent_id'] = '1'; // Garage
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
    }

    public function testCreateWithInvalidData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = ''; // Empty name
        $_POST['parent_id'] = '';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Location name is required', $result['errors']);
    }

    public function testCreateWithInvalidParent()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Child Location';
        $_POST['parent_id'] = '999'; // Non-existent parent
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Parent location not found', $result['errors']);
    }

    public function testCreateWithDuplicateName()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Garage'; // Already exists
        $_POST['parent_id'] = '';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Location name already exists in this parent location', $result['errors']);
    }

    public function testCreateWithLongName()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = str_repeat('a', 101); // 101 characters
        $_POST['parent_id'] = '';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Location name must be less than 100 characters', $result['errors']);
    }

    public function testCreateThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->create();
    }

    public function testUpdateWithValidData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Updated Garage';
        $_POST['parent_id'] = '';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('location', $result);
        
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertEquals('Location updated successfully', $result['message']);
    }

    public function testUpdateWithInvalidData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = ''; // Empty name
        $_POST['parent_id'] = '';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Location name is required', $result['errors']);
    }

    public function testUpdateWithCircularReference()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Updated Location';
        $_POST['parent_id'] = '1'; // Set parent to itself
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Location cannot be its own parent', $result['errors']);
    }

    public function testUpdateWithInvalidParent()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Updated Location';
        $_POST['parent_id'] = '999'; // Non-existent parent
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Parent location not found', $result['errors']);
    }

    public function testUpdateWithDuplicateName()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Storage Room'; // Already exists
        $_POST['parent_id'] = '';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Location name already exists in this parent location', $result['errors']);
    }

    public function testUpdateThrowsExceptionWhenLocationNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Location not found');
        $this->controller->update(999);
    }

    public function testUpdateThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->update(1);
    }

    public function testDeleteWithValidData()
    {
        $locationId = $this->createTestLocation('To Delete', null, 1);
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->delete($locationId);
        
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertEquals('Location deleted successfully', $result['message']);
    }

    public function testDeleteThrowsExceptionWhenLocationNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Location not found');
        $this->controller->delete(999);
    }

    public function testDeleteThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->delete(1);
    }

    public function testCreateWithSameNameInDifferentParent()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Workbench'; // Same name as existing child
        $_POST['parent_id'] = '3'; // Under Storage Room instead of Garage
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
    }

    public function testUpdateWithSameName()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Garage'; // Same as current name
        $_POST['parent_id'] = '';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
    }

    /**
     * Mock CSRF validation for testing
     */
    private function mockCSRFValidation($isValid)
    {
        // This would need to be implemented based on your Security class
        // For now, we'll assume the validation passes
        // In a real implementation, you might want to use a mocking framework
    }
}
