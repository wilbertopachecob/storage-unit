<?php
/**
 * Item Model Tests
 */

namespace StorageUnit\Tests\Unit\Models;

use StorageUnit\Tests\TestCase;
use StorageUnit\Models\Item;

class ItemTest extends TestCase
{
    public function testItemCreation()
    {
        $item = new Item('Test Item', 'Test Description', 5, 1, 'test.jpg', 1, 2);
        
        $this->assertEquals('Test Item', $item->getTitle());
        $this->assertEquals('Test Description', $item->getDescription());
        $this->assertEquals(5, $item->getQty());
        $this->assertEquals(1, $item->getUserId());
        $this->assertEquals(1, $item->getCategoryId());
        $this->assertEquals(2, $item->getLocationId());
        $this->assertEquals('test.jpg', $item->getImg());
    }

    public function testItemCreate()
    {
        $item = new Item('New Item', 'New Description', 3, 1, 'new.jpg', 1, 2);
        
        $this->assertTrue($item->create());
        $this->assertNotNull($item->getId());
    }

    public function testItemCreateWithInvalidData()
    {
        $item = new Item('', 'Description', 1, 1); // Empty title
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid item data');
        $item->create();
    }

    public function testItemCreateWithZeroQuantity()
    {
        $item = new Item('Test Item', 'Description', 0, 1); // Zero quantity
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid item data');
        $item->create();
    }

    public function testItemUpdate()
    {
        $item = Item::findById(1, 1);
        $item->setTitle('Updated Title');
        $item->setDescription('Updated Description');
        $item->setQty(10);
        
        $this->assertTrue($item->update());
        
        $updatedItem = Item::findById(1, 1);
        $this->assertEquals('Updated Title', $updatedItem->getTitle());
        $this->assertEquals('Updated Description', $updatedItem->getDescription());
        $this->assertEquals(10, $updatedItem->getQty());
    }

    public function testItemDelete()
    {
        $itemId = $this->createTestItem('To Delete', 'Will be deleted', 1, 1);
        
        $item = Item::findById($itemId, 1);
        $this->assertTrue($item->delete());
        
        $deletedItem = Item::findById($itemId, 1);
        $this->assertNull($deletedItem);
    }

    public function testFindById()
    {
        $item = Item::findById(1, 1);
        
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals(1, $item->getId());
        $this->assertEquals('Test Item 1', $item->getTitle());
    }

    public function testFindByIdWithWrongUser()
    {
        $item = Item::findById(1, 2); // Item belongs to user 1, but searching for user 2
        
        $this->assertNull($item);
    }

    public function testGetAllForUser()
    {
        $items = Item::getAllForUser(1);
        
        $this->assertIsArray($items);
        $this->assertCount(2, $items); // Should have 2 items for user 1
        $this->assertEquals('Test Item 2', $items[0]['title']); // Should be ordered by updated_at DESC
    }

    public function testGetAllForUserWithLimit()
    {
        $items = Item::getAllForUser(1, 1);
        
        $this->assertIsArray($items);
        $this->assertCount(1, $items);
    }

    public function testSearchByTitle()
    {
        $items = Item::searchByTitle('Test Item', 1);
        
        $this->assertIsArray($items);
        $this->assertCount(2, $items);
        
        foreach ($items as $item) {
            $this->assertStringContainsString('Test Item', $item['title']);
        }
    }

    public function testSearchByTitleWithNoResults()
    {
        $items = Item::searchByTitle('NonExistent', 1);
        
        $this->assertIsArray($items);
        $this->assertCount(0, $items);
    }

    public function testGetTotalQuantityForUser()
    {
        $total = Item::getTotalQuantityForUser(1);
        
        $this->assertEquals(3, $total); // 1 + 2 = 3
    }

    public function testGetCountForUser()
    {
        $count = Item::getCountForUser(1);
        
        $this->assertEquals(2, $count);
    }

    public function testToArray()
    {
        $item = Item::findById(1, 1);
        $array = $item->toArray();
        
        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Test Item 1', $array['title']);
        $this->assertEquals('Test description for item 1', $array['description']);
        $this->assertEquals(1, $array['qty']);
        $this->assertEquals(1, $array['user_id']);
    }

    public function testItemValidationWithEmptyTitle()
    {
        $item = new Item('', 'Description', 1, 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid item data');
        $item->create();
    }

    public function testItemValidationWithNoUserId()
    {
        $item = new Item('Title', 'Description', 1, null);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid item data');
        $item->create();
    }

    public function testItemValidationWithNegativeQuantity()
    {
        $item = new Item('Title', 'Description', -1, 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid item data');
        $item->create();
    }

    public function testEnhancedSearchWithNoFilters()
    {
        $items = Item::search('Test Item', 1);
        
        $this->assertIsArray($items);
        $this->assertCount(2, $items);
        
        foreach ($items as $item) {
            $this->assertArrayHasKey('category_name', $item);
            $this->assertArrayHasKey('category_color', $item);
            $this->assertArrayHasKey('location_name', $item);
        }
    }

    public function testEnhancedSearchWithCategoryFilter()
    {
        $items = Item::search('', 1, 1); // Search with Tools category
        
        $this->assertIsArray($items);
        $this->assertCount(1, $items);
        $this->assertEquals('Tools', $items[0]['category_name']);
    }

    public function testEnhancedSearchWithLocationFilter()
    {
        $items = Item::search('', 1, null, 2); // Search with Workbench location
        
        $this->assertIsArray($items);
        $this->assertCount(1, $items);
        $this->assertEquals('Workbench', $items[0]['location_name']);
    }

    public function testEnhancedSearchWithMultipleFilters()
    {
        $items = Item::search('Test', 1, 1, 2); // Search with category and location filters
        
        $this->assertIsArray($items);
        $this->assertCount(1, $items);
        $this->assertEquals('Tools', $items[0]['category_name']);
        $this->assertEquals('Workbench', $items[0]['location_name']);
    }

    public function testGetAllWithDetails()
    {
        $items = Item::getAllWithDetails(1);
        
        $this->assertIsArray($items);
        $this->assertCount(2, $items);
        
        foreach ($items as $item) {
            $this->assertArrayHasKey('category_name', $item);
            $this->assertArrayHasKey('category_color', $item);
            $this->assertArrayHasKey('category_icon', $item);
            $this->assertArrayHasKey('location_name', $item);
            $this->assertArrayHasKey('location_parent_id', $item);
        }
    }

    public function testItemSettersAndGetters()
    {
        $item = new Item();
        
        $item->setTitle('Test Title');
        $item->setDescription('Test Description');
        $item->setQty(5);
        $item->setUserId(1);
        $item->setCategoryId(2);
        $item->setLocationId(3);
        $item->setImg('test.jpg');
        
        $this->assertEquals('Test Title', $item->getTitle());
        $this->assertEquals('Test Description', $item->getDescription());
        $this->assertEquals(5, $item->getQty());
        $this->assertEquals(1, $item->getUserId());
        $this->assertEquals(2, $item->getCategoryId());
        $this->assertEquals(3, $item->getLocationId());
        $this->assertEquals('test.jpg', $item->getImg());
    }

    public function testToArrayIncludesNewFields()
    {
        $item = Item::findById(1, 1);
        $array = $item->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('category_id', $array);
        $this->assertArrayHasKey('location_id', $array);
        $this->assertEquals(1, $array['category_id']);
        $this->assertEquals(2, $array['location_id']);
    }
}
