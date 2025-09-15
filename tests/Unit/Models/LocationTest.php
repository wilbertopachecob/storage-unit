<?php
/**
 * Location Model Unit Tests
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use StorageUnit\Models\Location;
use StorageUnit\Core\Database;
use PDO;
use PDOStatement;

class LocationTest extends TestCase
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

    public function testLocationCreation()
    {
        $location = new Location('Test Location', 'Test Description', '123 Test St', 40.7128, -74.0060, 1, 2);
        
        $this->assertEquals('Test Location', $location->getName());
        $this->assertEquals('Test Description', $location->getDescription());
        $this->assertEquals('123 Test St', $location->getAddress());
        $this->assertEquals(40.7128, $location->getLatitude());
        $this->assertEquals(-74.0060, $location->getLongitude());
        $this->assertEquals(1, $location->getUserId());
        $this->assertEquals(2, $location->getParentId());
    }

    public function testLocationDefaultValues()
    {
        $location = new Location('Test Location');
        
        $this->assertEquals('Test Location', $location->getName());
        $this->assertNull($location->getDescription());
        $this->assertNull($location->getAddress());
        $this->assertNull($location->getLatitude());
        $this->assertNull($location->getLongitude());
        $this->assertNull($location->getUserId());
        $this->assertNull($location->getParentId());
    }

    public function testLocationSetters()
    {
        $location = new Location();
        
        $location->setName('New Location');
        $location->setDescription('New Description');
        $location->setAddress('456 New St');
        $location->setLatitude(41.8781);
        $location->setLongitude(-87.6298);
        $location->setUserId(2);
        $location->setParentId(3);
        
        $this->assertEquals('New Location', $location->getName());
        $this->assertEquals('New Description', $location->getDescription());
        $this->assertEquals('456 New St', $location->getAddress());
        $this->assertEquals(41.8781, $location->getLatitude());
        $this->assertEquals(-87.6298, $location->getLongitude());
        $this->assertEquals(2, $location->getUserId());
        $this->assertEquals(3, $location->getParentId());
    }

    public function testCreateLocation()
    {
        $location = new Location('Test Location', 'Test Description', '123 Test St', 40.7128, -74.0060, 1, 2);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
            
        $this->mockConnection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('123');
        
        $result = $location->create();
        
        $this->assertTrue($result);
        $this->assertEquals(123, $location->getId());
    }

    public function testCreateLocationFails()
    {
        $location = new Location('Test Location', 'Test Description', '123 Test St', 40.7128, -74.0060, 1, 2);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(false);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $location->create();
        
        $this->assertFalse($result);
    }

    public function testUpdateLocation()
    {
        $location = new Location('Test Location', 'Test Description', '123 Test St', 40.7128, -74.0060, 1, 2);
        $location->setId(123);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $location->update();
        
        $this->assertTrue($result);
    }

    public function testDeleteLocation()
    {
        $location = new Location('Test Location', 'Test Description', '123 Test St', 40.7128, -74.0060, 1, 2);
        $location->setId(123);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $location->delete();
        
        $this->assertTrue($result);
    }

    public function testFindById()
    {
        $mockData = [
            'id' => 123,
            'name' => 'Test Location',
            'description' => 'Test Description',
            'address' => '123 Test St',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'parent_id' => 2,
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
        
        $location = Location::findById(123, 1);
        
        $this->assertInstanceOf(Location::class, $location);
        $this->assertEquals(123, $location->getId());
        $this->assertEquals('Test Location', $location->getName());
        $this->assertEquals(40.7128, $location->getLatitude());
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
        
        $location = Location::findById(999, 1);
        
        $this->assertNull($location);
    }

    public function testGetWithItemCount()
    {
        $mockData = [
            [
                'id' => 1,
                'name' => 'Location 1',
                'description' => 'Description 1',
                'item_count' => 5
            ],
            [
                'id' => 2,
                'name' => 'Location 2',
                'description' => 'Description 2',
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
        
        $locations = Location::getWithItemCount(1, '', 'name', 'asc', 10, 0);
        
        $this->assertCount(2, $locations);
        $this->assertEquals('Location 1', $locations[0]['name']);
        $this->assertEquals(5, $locations[0]['item_count']);
    }

    public function testGetWithItemCountWithSearch()
    {
        $mockData = [
            [
                'id' => 1,
                'name' => 'Garage',
                'description' => 'Main garage',
                'item_count' => 5
            ]
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':user_id' => 1, ':search' => '%garage%', ':limit' => 10, ':offset' => 0]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $locations = Location::getWithItemCount(1, 'garage', 'name', 'asc', 10, 0);
        
        $this->assertCount(1, $locations);
        $this->assertEquals('Garage', $locations[0]['name']);
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
        
        $count = Location::getCountForUser(1);
        
        $this->assertEquals(3, $count);
    }

    public function testGetCountForUserWithSearch()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':user_id' => 1, ':search' => '%garage%']);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $count = Location::getCountForUser(1, 'garage');
        
        $this->assertEquals(1, $count);
    }

    public function testGetItemsInLocation()
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
                'location_name' => 'Garage'
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
        
        $items = Location::getItemsInLocation(1, 1);
        
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
        
        $count = Location::getItemCount(1, 1);
        
        $this->assertEquals(5, $count);
    }

    public function testNameExists()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':name' => 'Test Location', ':user_id' => 1]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = Location::nameExists('Test Location', 1);
        
        $this->assertTrue($exists);
    }

    public function testNameExistsWithParentId()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':name' => 'Test Location', ':user_id' => 1, ':parent_id' => 2]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(0);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = Location::nameExists('Test Location', 1, 2);
        
        $this->assertFalse($exists);
    }

    public function testNameExistsWithExcludeId()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':name' => 'Test Location', ':user_id' => 1, ':exclude_id' => 123]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(0);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = Location::nameExists('Test Location', 1, null, 123);
        
        $this->assertFalse($exists);
    }

    public function testGetFullPath()
    {
        // Mock current location
        $location = new Location('Child Location');
        $location->setId(1);
        $location->setParentId(2);
        $location->setUserId(1);
        
        // Mock the findById call for parent
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':id' => 2, ':user_id' => 1]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'id' => 2,
                'name' => 'Parent Location',
                'description' => null,
                'address' => null,
                'latitude' => null,
                'longitude' => null,
                'parent_id' => null,
                'user_id' => 1,
                'created_at' => '2024-01-01 00:00:00',
                'updated_at' => '2024-01-01 00:00:00'
            ]);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $path = $location->getFullPath();
        
        $this->assertEquals('Parent Location → Child Location', $path);
    }

    public function testToArray()
    {
        $location = new Location('Test Location', 'Test Description', '123 Test St', 40.7128, -74.0060, 1, 2);
        $location->setId(123);
        $location->setCreatedAt('2024-01-01 00:00:00');
        $location->setUpdatedAt('2024-01-01 00:00:00');
        
        $array = $location->toArray();
        
        $expected = [
            'id' => 123,
            'name' => 'Test Location',
            'description' => 'Test Description',
            'address' => '123 Test St',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'parent_id' => 2,
            'user_id' => 1,
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->assertEquals($expected, $array);
    }
}