<?php
/**
 * Item Model Unit Tests
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use StorageUnit\Models\Item;
use StorageUnit\Core\Database;
use PDO;
use PDOStatement;

class ItemTest extends TestCase
{
    private $mockDb;
    private $mockConnection;
    private $mockStatement;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock database connection
        $this->mockStatement = $this->createMock(PDOStatement::class);
        $this->mockConnection = $this->createMock(PDO::class);
        $this->mockDb = $this->createMock(Database::class);
        
        // Set up database mock
        $this->mockDb->method('getConnection')->willReturn($this->mockConnection);
        Database::setInstance($this->mockDb);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Database::setInstance(null);
    }

    public function testItemCreation()
    {
        $item = new Item('Test Item', 'Test Description', 5, 1, 'test.jpg', 1, 2);
        
        $this->assertEquals('Test Item', $item->getTitle());
        $this->assertEquals('Test Description', $item->getDescription());
        $this->assertEquals(5, $item->getQty());
        $this->assertEquals(1, $item->getUserId());
        $this->assertEquals('test.jpg', $item->getImg());
        $this->assertEquals(1, $item->getCategoryId());
        $this->assertEquals(2, $item->getLocationId());
    }

    public function testItemSetters()
    {
        $item = new Item();
        
        $item->setTitle('New Title');
        $item->setDescription('New Description');
        $item->setQty(10);
        $item->setUserId(2);
        $item->setImg('new.jpg');
        $item->setCategoryId(3);
        $item->setLocationId(4);
        
        $this->assertEquals('New Title', $item->getTitle());
        $this->assertEquals('New Description', $item->getDescription());
        $this->assertEquals(10, $item->getQty());
        $this->assertEquals(2, $item->getUserId());
        $this->assertEquals('new.jpg', $item->getImg());
        $this->assertEquals(3, $item->getCategoryId());
        $this->assertEquals(4, $item->getLocationId());
    }

    public function testCreateItem()
    {
        $item = new Item('Test Item', 'Test Description', 5, 1, 'test.jpg', 1, 2);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
            
        $this->mockConnection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('123');
        
        $result = $item->create();
        
        $this->assertTrue($result);
        $this->assertEquals(123, $item->getId());
    }

    public function testCreateItemFails()
    {
        $item = new Item('Test Item', 'Test Description', 5, 1, 'test.jpg', 1, 2);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(false);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $item->create();
        
        $this->assertFalse($result);
    }

    public function testUpdateItem()
    {
        $item = new Item('Test Item', 'Test Description', 5, 1, 'test.jpg', 1, 2);
        $item->setId(123);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $item->update();
        
        $this->assertTrue($result);
    }

    public function testUpdateItemWithoutId()
    {
        $item = new Item('Test Item', 'Test Description', 5, 1, 'test.jpg', 1, 2);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item ID is required for update');
        
        $item->update();
    }

    public function testDeleteItem()
    {
        $item = new Item('Test Item', 'Test Description', 5, 1, 'test.jpg', 1, 2);
        $item->setId(123);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $item->delete();
        
        $this->assertTrue($result);
    }

    public function testFindById()
    {
        $mockData = [
            'id' => 123,
            'title' => 'Test Item',
            'description' => 'Test Description',
            'qty' => 5,
            'user_id' => 1,
            'category_id' => 1,
            'location_id' => 2,
            'img' => 'test.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':id' => 123, ':user_id' => 1]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $item = Item::findById(123, 1);
        
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals(123, $item->getId());
        $this->assertEquals('Test Item', $item->getTitle());
    }

    public function testFindByIdNotFound()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':id' => 999, ':user_id' => 1]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn(false);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $item = Item::findById(999, 1);
        
        $this->assertNull($item);
    }

    public function testGetAllWithDetails()
    {
        $mockData = [
            [
                'id' => 1,
                'title' => 'Item 1',
                'category_name' => 'Category 1',
                'location_name' => 'Location 1'
            ],
            [
                'id' => 2,
                'title' => 'Item 2',
                'category_name' => 'Category 2',
                'location_name' => 'Location 2'
            ]
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':user_id' => 1, ':limit' => 10, ':offset' => 0]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $items = Item::getAllWithDetails(1, '', null, null, 'created_at', 'desc', 10, 0);
        
        $this->assertCount(2, $items);
        $this->assertEquals('Item 1', $items[0]['title']);
    }

    public function testGetAllWithDetailsWithSearch()
    {
        $mockData = [
            [
                'id' => 1,
                'title' => 'Hammer',
                'category_name' => 'Tools',
                'location_name' => 'Garage'
            ]
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':user_id' => 1, ':search' => '%hammer%', ':limit' => 10, ':offset' => 0]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $items = Item::getAllWithDetails(1, 'hammer', null, null, 'created_at', 'desc', 10, 0);
        
        $this->assertCount(1, $items);
        $this->assertEquals('Hammer', $items[0]['title']);
    }

    public function testGetCountForUser()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':user_id' => 1]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(5);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $count = Item::getCountForUser(1);
        
        $this->assertEquals(5, $count);
    }

    public function testGetCountWithImages()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with();
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(3);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $count = Item::getCountWithImages(1);
        
        $this->assertEquals(3, $count);
    }

    public function testGetCountWithoutImages()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with();
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(2);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $count = Item::getCountWithoutImages(1);
        
        $this->assertEquals(2, $count);
    }

    public function testGetRecentItemsCount()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with();
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(4);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $count = Item::getRecentItemsCount(1, 7);
        
        $this->assertEquals(4, $count);
    }

    public function testGetTotalQuantityForUser()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with();
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(25);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $total = Item::getTotalQuantityForUser(1);
        
        $this->assertEquals(25, $total);
    }

    public function testToArray()
    {
        $item = new Item('Test Item', 'Test Description', 5, 1, 'test.jpg', 1, 2);
        $item->setId(123);
        $item->setCreatedAt('2024-01-01 00:00:00');
        $item->setUpdatedAt('2024-01-01 00:00:00');
        
        $array = $item->toArray();
        
        $expected = [
            'id' => 123,
            'title' => 'Test Item',
            'description' => 'Test Description',
            'qty' => 5,
            'user_id' => 1,
            'category_id' => 1,
            'location_id' => 2,
            'img' => 'test.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->assertEquals($expected, $array);
    }
}