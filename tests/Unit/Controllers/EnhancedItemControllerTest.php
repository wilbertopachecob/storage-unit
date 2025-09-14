<?php
/**
 * Enhanced Item Controller Tests
 */

namespace StorageUnit\Tests\Unit\Controllers;

use StorageUnit\Tests\TestCase;
use StorageUnit\Controllers\EnhancedItemController;
use StorageUnit\Models\Item;
use StorageUnit\Models\Category;
use StorageUnit\Models\Location;

class EnhancedItemControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new EnhancedItemController();
        $this->authenticateUser(1);
    }

    public function testIndexReturnsItemsWithDetails()
    {
        $result = $this->controller->index();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('locations', $result);
        $this->assertArrayHasKey('total_quantity', $result);
        $this->assertArrayHasKey('total_count', $result);
        
        $this->assertCount(2, $result['items']); // Should have 2 items for user 1
        $this->assertCount(3, $result['categories']); // Should have 3 categories for user 1
        $this->assertCount(3, $result['locations']); // Should have 3 locations for user 1
    }

    public function testIndexThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->index();
    }

    public function testShowReturnsItemWithDetails()
    {
        $result = $this->controller->show(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('item', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('location', $result);
        
        $this->assertInstanceOf(Item::class, $result['item']);
        $this->assertEquals(1, $result['item']->getId());
        $this->assertEquals('Test Item 1', $result['item']->getTitle());
    }

    public function testShowThrowsExceptionWhenItemNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item not found');
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
        $_POST['title'] = 'New Test Item';
        $_POST['description'] = 'New test description';
        $_POST['qty'] = 5;
        $_POST['category_id'] = 1;
        $_POST['location_id'] = 2;
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('locations', $result);
        
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
    }

    public function testCreateWithInvalidData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['title'] = ''; // Empty title
        $_POST['description'] = 'Test description';
        $_POST['qty'] = 1;
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Title is required', $result['errors']);
    }

    public function testCreateWithInvalidCategory()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['title'] = 'Test Item';
        $_POST['description'] = 'Test description';
        $_POST['qty'] = 1;
        $_POST['category_id'] = 999; // Non-existent category
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Selected category not found', $result['errors']);
    }

    public function testCreateWithInvalidLocation()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['title'] = 'Test Item';
        $_POST['description'] = 'Test description';
        $_POST['qty'] = 1;
        $_POST['location_id'] = 999; // Non-existent location
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Selected location not found', $result['errors']);
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
        $_POST['title'] = 'Updated Test Item';
        $_POST['description'] = 'Updated test description';
        $_POST['qty'] = 10;
        $_POST['category_id'] = 2;
        $_POST['location_id'] = 3;
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('item', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('locations', $result);
        
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
    }

    public function testUpdateThrowsExceptionWhenItemNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item not found');
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
        $itemId = $this->createTestItem('To Delete', 'Will be deleted', 1, 1, 'delete.jpg', 1, 2);
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->delete($itemId);
        
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
    }

    public function testDeleteThrowsExceptionWhenItemNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item not found');
        $this->controller->delete(999);
    }

    public function testDeleteThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->delete(1);
    }

    public function testSearchWithNoFilters()
    {
        $_GET['q'] = 'Test Item';
        
        $result = $this->controller->search();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('locations', $result);
        $this->assertArrayHasKey('search_term', $result);
        $this->assertArrayHasKey('total_count', $result);
        
        $this->assertEquals('Test Item', $result['search_term']);
        $this->assertCount(2, $result['items']); // Should find 2 items
    }

    public function testSearchWithCategoryFilter()
    {
        $_GET['q'] = '';
        $_GET['category_id'] = 1; // Tools category
        
        $result = $this->controller->search();
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result['items']); // Should find 1 item in Tools category
        $this->assertEquals(1, $result['selected_category']);
    }

    public function testSearchWithLocationFilter()
    {
        $_GET['q'] = '';
        $_GET['location_id'] = 2; // Workbench location
        
        $result = $this->controller->search();
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result['items']); // Should find 1 item in Workbench location
        $this->assertEquals(2, $result['selected_location']);
    }

    public function testSearchWithMultipleFilters()
    {
        $_GET['q'] = 'Test';
        $_GET['category_id'] = 1; // Tools category
        $_GET['location_id'] = 2; // Workbench location
        
        $result = $this->controller->search();
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result['items']); // Should find 1 item matching all criteria
        $this->assertEquals(1, $result['selected_category']);
        $this->assertEquals(2, $result['selected_location']);
    }

    public function testSearchThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->search();
    }

    public function testAnalyticsReturnsCorrectData()
    {
        $result = $this->controller->analytics();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_items', $result);
        $this->assertArrayHasKey('total_quantity', $result);
        $this->assertArrayHasKey('items_by_category', $result);
        $this->assertArrayHasKey('items_by_location', $result);
        $this->assertArrayHasKey('recent_items', $result);
        
        $this->assertEquals(2, $result['total_items']);
        $this->assertEquals(3, $result['total_quantity']); // 1 + 2 = 3
        $this->assertCount(3, $result['items_by_category']);
        $this->assertCount(3, $result['items_by_location']);
        $this->assertCount(2, $result['recent_items']); // Should have 2 recent items
    }

    public function testAnalyticsThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->analytics();
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
