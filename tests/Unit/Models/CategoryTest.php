<?php
/**
 * Category Model Tests
 */

namespace StorageUnit\Tests\Unit\Models;

use StorageUnit\Tests\TestCase;
use StorageUnit\Models\Category;

class CategoryTest extends TestCase
{
    public function testCategoryCreation()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', 1);
        
        $this->assertEquals('Test Category', $category->getName());
        $this->assertEquals('#ff0000', $category->getColor());
        $this->assertEquals('fas fa-test', $category->getIcon());
        $this->assertEquals(1, $category->getUserId());
    }

    public function testCategoryCreationWithDefaults()
    {
        $category = new Category('Test Category', null, null, 1);
        
        $this->assertEquals('Test Category', $category->getName());
        $this->assertEquals('#007bff', $category->getColor());
        $this->assertEquals('fas fa-box', $category->getIcon());
        $this->assertEquals(1, $category->getUserId());
    }

    public function testCategoryCreate()
    {
        $category = new Category('New Category', '#28a745', 'fas fa-tools', 1);
        
        $this->assertTrue($category->create());
        $this->assertNotNull($category->getId());
    }

    public function testCategoryCreateWithInvalidData()
    {
        $category = new Category('', '#ff0000', 'fas fa-test', 1); // Empty name
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid category data');
        $category->create();
    }

    public function testCategoryCreateWithNoUserId()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', null);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid category data');
        $category->create();
    }

    public function testCategoryCreateWithLongName()
    {
        $longName = str_repeat('a', 101); // 101 characters
        $category = new Category($longName, '#ff0000', 'fas fa-test', 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid category data');
        $category->create();
    }

    public function testCategoryUpdate()
    {
        $category = Category::findById(1, 1);
        $category->setName('Updated Category');
        $category->setColor('#ff0000');
        $category->setIcon('fas fa-updated');
        
        $this->assertTrue($category->update());
        
        $updatedCategory = Category::findById(1, 1);
        $this->assertEquals('Updated Category', $updatedCategory->getName());
        $this->assertEquals('#ff0000', $updatedCategory->getColor());
        $this->assertEquals('fas fa-updated', $updatedCategory->getIcon());
    }

    public function testCategoryDelete()
    {
        $categoryId = $this->createTestCategory('To Delete', '#ff0000', 'fas fa-delete', 1);
        
        $category = Category::findById($categoryId, 1);
        $this->assertTrue($category->delete());
        
        $deletedCategory = Category::findById($categoryId, 1);
        $this->assertNull($deletedCategory);
    }

    public function testFindById()
    {
        $category = Category::findById(1, 1);
        
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals(1, $category->getId());
        $this->assertEquals('Tools', $category->getName());
        $this->assertEquals('#28a745', $category->getColor());
        $this->assertEquals('fas fa-tools', $category->getIcon());
    }

    public function testFindByIdWithWrongUser()
    {
        $category = Category::findById(1, 2); // Category belongs to user 1, but searching for user 2
        
        $this->assertNull($category);
    }

    public function testGetAllForUser()
    {
        $categories = Category::getAllForUser(1);
        
        $this->assertIsArray($categories);
        $this->assertCount(3, $categories); // Should have 3 categories for user 1
        $this->assertEquals('Electronics', $categories[0]['name']); // Should be ordered by name ASC
    }

    public function testGetWithItemCount()
    {
        $categories = Category::getWithItemCount(1);
        
        $this->assertIsArray($categories);
        $this->assertCount(3, $categories);
        
        // Find Tools category (should have 1 item)
        $toolsCategory = array_filter($categories, function($cat) {
            return $cat['name'] === 'Tools';
        });
        $toolsCategory = array_values($toolsCategory)[0];
        $this->assertEquals(1, $toolsCategory['item_count']);
        
        // Find Electronics category (should have 1 item)
        $electronicsCategory = array_filter($categories, function($cat) {
            return $cat['name'] === 'Electronics';
        });
        $electronicsCategory = array_values($electronicsCategory)[0];
        $this->assertEquals(1, $electronicsCategory['item_count']);
    }

    public function testNameExists()
    {
        // Test existing name
        $this->assertTrue(Category::nameExists('Tools', 1));
        
        // Test non-existing name
        $this->assertFalse(Category::nameExists('NonExistent', 1));
        
        // Test name exists for different user
        $this->assertFalse(Category::nameExists('Tools', 2));
        
        // Test name exists excluding current category
        $this->assertFalse(Category::nameExists('Tools', 1, 1)); // Exclude Tools category with ID 1
    }

    public function testToArray()
    {
        $category = Category::findById(1, 1);
        $array = $category->toArray();
        
        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Tools', $array['name']);
        $this->assertEquals('#28a745', $array['color']);
        $this->assertEquals('fas fa-tools', $array['icon']);
        $this->assertEquals(1, $array['user_id']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }

    public function testCategoryValidationWithEmptyName()
    {
        $category = new Category('', '#ff0000', 'fas fa-test', 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid category data');
        $category->create();
    }

    public function testCategoryValidationWithWhitespaceName()
    {
        $category = new Category('   ', '#ff0000', 'fas fa-test', 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid category data');
        $category->create();
    }

    public function testCategoryValidationWithNoUserId()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', null);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid category data');
        $category->create();
    }

    public function testCategoryValidationWithLongName()
    {
        $longName = str_repeat('a', 101); // 101 characters
        $category = new Category($longName, '#ff0000', 'fas fa-test', 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid category data');
        $category->create();
    }

    public function testCategorySetters()
    {
        $category = new Category();
        
        $category->setName('Test Name');
        $category->setColor('#ff0000');
        $category->setIcon('fas fa-test');
        $category->setUserId(1);
        
        $this->assertEquals('Test Name', $category->getName());
        $this->assertEquals('#ff0000', $category->getColor());
        $this->assertEquals('fas fa-test', $category->getIcon());
        $this->assertEquals(1, $category->getUserId());
    }

    public function testCategoryGetters()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', 1);
        
        $this->assertEquals('Test Category', $category->getName());
        $this->assertEquals('#ff0000', $category->getColor());
        $this->assertEquals('fas fa-test', $category->getIcon());
        $this->assertEquals(1, $category->getUserId());
        $this->assertNull($category->getId());
        $this->assertNull($category->getCreatedAt());
        $this->assertNull($category->getUpdatedAt());
    }
}
