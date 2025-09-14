<?php
/**
 * Category Controller Tests
 */

namespace StorageUnit\Tests\Unit\Controllers;

use StorageUnit\Tests\TestCase;
use StorageUnit\Controllers\CategoryController;
use StorageUnit\Models\Category;

class CategoryControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new CategoryController();
        $this->authenticateUser(1);
    }

    public function testIndexReturnsCategoriesWithItemCount()
    {
        $result = $this->controller->index();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('total_count', $result);
        
        $this->assertCount(3, $result['categories']); // Should have 3 categories for user 1
        $this->assertEquals(3, $result['total_count']);
        
        // Check that categories have item_count
        foreach ($result['categories'] as $category) {
            $this->assertArrayHasKey('item_count', $category);
        }
    }

    public function testIndexThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->index();
    }

    public function testShowReturnsCategory()
    {
        $result = $this->controller->show(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('category', $result);
        
        $this->assertInstanceOf(Category::class, $result['category']);
        $this->assertEquals(1, $result['category']->getId());
        $this->assertEquals('Tools', $result['category']->getName());
    }

    public function testShowThrowsExceptionWhenCategoryNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');
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
        $_POST['name'] = 'New Category';
        $_POST['color'] = '#ff0000';
        $_POST['icon'] = 'fas fa-test';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('message', $result);
        
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertEquals('Category created successfully', $result['message']);
    }

    public function testCreateWithInvalidData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = ''; // Empty name
        $_POST['color'] = '#ff0000';
        $_POST['icon'] = 'fas fa-test';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Category name is required', $result['errors']);
    }

    public function testCreateWithDuplicateName()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Tools'; // Already exists
        $_POST['color'] = '#ff0000';
        $_POST['icon'] = 'fas fa-test';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Category name already exists', $result['errors']);
    }

    public function testCreateWithLongName()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = str_repeat('a', 101); // 101 characters
        $_POST['color'] = '#ff0000';
        $_POST['icon'] = 'fas fa-test';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->create();
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Category name must be less than 100 characters', $result['errors']);
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
        $_POST['name'] = 'Updated Tools';
        $_POST['color'] = '#00ff00';
        $_POST['icon'] = 'fas fa-updated';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('category', $result);
        
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertEquals('Category updated successfully', $result['message']);
    }

    public function testUpdateWithInvalidData()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = ''; // Empty name
        $_POST['color'] = '#ff0000';
        $_POST['icon'] = 'fas fa-test';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Category name is required', $result['errors']);
    }

    public function testUpdateWithDuplicateName()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'Electronics'; // Already exists
        $_POST['color'] = '#ff0000';
        $_POST['icon'] = 'fas fa-test';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->update(1);
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertContains('Category name already exists', $result['errors']);
    }

    public function testUpdateThrowsExceptionWhenCategoryNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');
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
        $categoryId = $this->createTestCategory('To Delete', '#ff0000', 'fas fa-delete', 1);
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        
        // Mock CSRF validation
        $this->mockCSRFValidation(true);
        
        $result = $this->controller->delete($categoryId);
        
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertEquals('Category deleted successfully', $result['message']);
    }

    public function testDeleteThrowsExceptionWhenCategoryNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');
        $this->controller->delete(999);
    }

    public function testDeleteThrowsExceptionWhenNotAuthenticated()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->controller->delete(1);
    }

    public function testCreateWithDefaultValues()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'valid_token';
        $_POST['name'] = 'New Category';
        // No color or icon provided - should use defaults
        
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
        $_POST['name'] = 'Tools'; // Same as current name
        $_POST['color'] = '#00ff00';
        $_POST['icon'] = 'fas fa-updated';
        
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
