<?php
/**
 * Category Model Unit Tests
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use StorageUnit\Models\Category;
use StorageUnit\Core\Database;
use PDO;
use PDOStatement;

class CategoryTest extends TestCase
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

    public function testCategoryCreation()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', 1);
        
        $this->assertEquals('Test Category', $category->getName());
        $this->assertEquals('#ff0000', $category->getColor());
        $this->assertEquals('fas fa-test', $category->getIcon());
        $this->assertEquals(1, $category->getUserId());
    }

    public function testCategoryDefaultValues()
    {
        $category = new Category('Test Category', null, null, 1);
        
        $this->assertEquals('Test Category', $category->getName());
        $this->assertEquals('#007bff', $category->getColor());
        $this->assertEquals('fas fa-box', $category->getIcon());
        $this->assertEquals(1, $category->getUserId());
    }

    public function testCategorySetters()
    {
        $category = new Category();
        
        $category->setName('New Category');
        $category->setColor('#00ff00');
        $category->setIcon('fas fa-new');
        $category->setUserId(2);
        
        $this->assertEquals('New Category', $category->getName());
        $this->assertEquals('#00ff00', $category->getColor());
        $this->assertEquals('fas fa-new', $category->getIcon());
        $this->assertEquals(2, $category->getUserId());
    }

    public function testCreateCategory()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', 1);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
            
        $this->mockConnection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('123');
        
        $result = $category->create();
        
        $this->assertTrue($result);
        $this->assertEquals(123, $category->getId());
    }

    public function testCreateCategoryFails()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', 1);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(false);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $category->create();
        
        $this->assertFalse($result);
    }

    public function testUpdateCategory()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', 1);
        $category->setId(123);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $category->update();
        
        $this->assertTrue($result);
    }

    public function testDeleteCategory()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', 1);
        $category->setId(123);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $category->delete();
        
        $this->assertTrue($result);
    }

    public function testFindById()
    {
        $mockData = [
            'id' => 123,
            'name' => 'Test Category',
            'color' => '#ff0000',
            'icon' => 'fas fa-test',
            'user_id' => 1,
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
        
        $category = Category::findById(123, 1);
        
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals(123, $category->getId());
        $this->assertEquals('Test Category', $category->getName());
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
        
        $category = Category::findById(999, 1);
        
        $this->assertNull($category);
    }

    public function testGetWithItemCount()
    {
        $mockData = [
            [
                'id' => 1,
                'name' => 'Category 1',
                'color' => '#ff0000',
                'icon' => 'fas fa-test',
                'item_count' => 5
            ],
            [
                'id' => 2,
                'name' => 'Category 2',
                'color' => '#00ff00',
                'icon' => 'fas fa-test2',
                'item_count' => 3
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
        
        $categories = Category::getWithItemCount(1, '', 'name', 'asc', 10, 0);
        
        $this->assertCount(2, $categories);
        $this->assertEquals('Category 1', $categories[0]['name']);
        $this->assertEquals(5, $categories[0]['item_count']);
    }

    public function testGetWithItemCountWithSearch()
    {
        $mockData = [
            [
                'id' => 1,
                'name' => 'Tools',
                'color' => '#ff0000',
                'icon' => 'fas fa-tools',
                'item_count' => 5
            ]
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':user_id' => 1, ':search' => '%tools%', ':limit' => 10, ':offset' => 0]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $categories = Category::getWithItemCount(1, 'tools', 'name', 'asc', 10, 0);
        
        $this->assertCount(1, $categories);
        $this->assertEquals('Tools', $categories[0]['name']);
    }

    public function testGetCountForUser()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':user_id' => 1]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(3);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $count = Category::getCountForUser(1);
        
        $this->assertEquals(3, $count);
    }

    public function testGetCountForUserWithSearch()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':user_id' => 1, ':search' => '%tools%']);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $count = Category::getCountForUser(1, 'tools');
        
        $this->assertEquals(1, $count);
    }

    public function testGetItemsInCategory()
    {
        $mockData = [
            [
                'id' => 1,
                'title' => 'Item 1',
                'category_name' => 'Tools',
                'location_name' => 'Garage'
            ],
            [
                'id' => 2,
                'title' => 'Item 2',
                'category_name' => 'Tools',
                'location_name' => 'Basement'
            ]
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with();
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $items = Category::getItemsInCategory(1, 1);
        
        $this->assertCount(2, $items);
        $this->assertEquals('Item 1', $items[0]['title']);
    }

    public function testGetItemCount()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with();
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(5);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $count = Category::getItemCount(1, 1);
        
        $this->assertEquals(5, $count);
    }

    public function testNameExists()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':name' => 'Test Category', ':user_id' => 1]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = Category::nameExists('Test Category', 1);
        
        $this->assertTrue($exists);
    }

    public function testNameExistsWithExcludeId()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':name' => 'Test Category', ':user_id' => 1, ':exclude_id' => 123]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(0);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = Category::nameExists('Test Category', 1, 123);
        
        $this->assertFalse($exists);
    }

    public function testToArray()
    {
        $category = new Category('Test Category', '#ff0000', 'fas fa-test', 1);
        $category->setId(123);
        $category->setCreatedAt('2024-01-01 00:00:00');
        $category->setUpdatedAt('2024-01-01 00:00:00');
        
        $array = $category->toArray();
        
        $expected = [
            'id' => 123,
            'name' => 'Test Category',
            'color' => '#ff0000',
            'icon' => 'fas fa-test',
            'user_id' => 1,
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->assertEquals($expected, $array);
    }
}