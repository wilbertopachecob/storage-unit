<?php
/**
 * Location Model Tests
 */

namespace StorageUnit\Tests\Unit\Models;

use StorageUnit\Tests\TestCase;
use StorageUnit\Models\Location;

class LocationTest extends TestCase
{
    public function testLocationCreation()
    {
        $location = new Location('Test Location', 1, 1);
        
        $this->assertEquals('Test Location', $location->getName());
        $this->assertEquals(1, $location->getParentId());
        $this->assertEquals(1, $location->getUserId());
    }

    public function testLocationCreationWithDefaults()
    {
        $location = new Location('Test Location', null, 1);
        
        $this->assertEquals('Test Location', $location->getName());
        $this->assertNull($location->getParentId());
        $this->assertEquals(1, $location->getUserId());
    }

    public function testLocationCreate()
    {
        $location = new Location('New Location', null, 1);
        
        $this->assertTrue($location->create());
        $this->assertNotNull($location->getId());
    }

    public function testLocationCreateWithParent()
    {
        $parentId = $this->createTestLocation('Parent Location', null, 1);
        $location = new Location('Child Location', $parentId, 1);
        
        $this->assertTrue($location->create());
        $this->assertNotNull($location->getId());
    }

    public function testLocationCreateWithInvalidData()
    {
        $location = new Location('', null, 1); // Empty name
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid location data');
        $location->create();
    }

    public function testLocationCreateWithNoUserId()
    {
        $location = new Location('Test Location', null, null);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid location data');
        $location->create();
    }

    public function testLocationCreateWithLongName()
    {
        $longName = str_repeat('a', 101); // 101 characters
        $location = new Location($longName, null, 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid location data');
        $location->create();
    }

    public function testLocationUpdate()
    {
        $location = Location::findById(1, 1);
        $location->setName('Updated Location');
        $location->setParentId(2);
        
        $this->assertTrue($location->update());
        
        $updatedLocation = Location::findById(1, 1);
        $this->assertEquals('Updated Location', $updatedLocation->getName());
        $this->assertEquals(2, $updatedLocation->getParentId());
    }

    public function testLocationDelete()
    {
        $locationId = $this->createTestLocation('To Delete', null, 1);
        
        $location = Location::findById($locationId, 1);
        $this->assertTrue($location->delete());
        
        $deletedLocation = Location::findById($locationId, 1);
        $this->assertNull($deletedLocation);
    }

    public function testFindById()
    {
        $location = Location::findById(1, 1);
        
        $this->assertInstanceOf(Location::class, $location);
        $this->assertEquals(1, $location->getId());
        $this->assertEquals('Garage', $location->getName());
        $this->assertNull($location->getParentId());
        $this->assertEquals(1, $location->getUserId());
    }

    public function testFindByIdWithWrongUser()
    {
        $location = Location::findById(1, 2); // Location belongs to user 1, but searching for user 2
        
        $this->assertNull($location);
    }

    public function testGetAllForUser()
    {
        $locations = Location::getAllForUser(1);
        
        $this->assertIsArray($locations);
        $this->assertCount(3, $locations); // Should have 3 locations for user 1
        $this->assertEquals('Garage', $locations[0]['name']); // Should be ordered by name ASC
    }

    public function testGetHierarchy()
    {
        $hierarchy = Location::getHierarchy(1);
        
        $this->assertIsArray($hierarchy);
        $this->assertCount(2, $hierarchy); // Should have 2 root locations (Garage, Storage Room)
        
        // Find Garage location
        $garage = array_filter($hierarchy, function($loc) {
            return $loc['name'] === 'Garage';
        });
        $garage = array_values($garage)[0];
        $this->assertArrayHasKey('children', $garage);
        $this->assertCount(1, $garage['children']); // Should have Workbench as child
        $this->assertEquals('Workbench', $garage['children'][0]['name']);
    }

    public function testGetWithItemCount()
    {
        $locations = Location::getWithItemCount(1);
        
        $this->assertIsArray($locations);
        $this->assertCount(3, $locations);
        
        // Find Workbench location (should have 1 item)
        $workbench = array_filter($locations, function($loc) {
            return $loc['name'] === 'Workbench';
        });
        $workbench = array_values($workbench)[0];
        $this->assertEquals(1, $workbench['item_count']);
        
        // Find Storage Room location (should have 1 item)
        $storageRoom = array_filter($locations, function($loc) {
            return $loc['name'] === 'Storage Room';
        });
        $storageRoom = array_values($storageRoom)[0];
        $this->assertEquals(1, $storageRoom['item_count']);
    }

    public function testGetFullPath()
    {
        $workbench = Location::findById(2, 1); // Workbench (child of Garage)
        $fullPath = $workbench->getFullPath();
        
        $this->assertEquals('Garage → Workbench', $fullPath);
    }

    public function testGetFullPathForRootLocation()
    {
        $garage = Location::findById(1, 1); // Garage (root location)
        $fullPath = $garage->getFullPath();
        
        $this->assertEquals('Garage', $fullPath);
    }

    public function testNameExists()
    {
        // Test existing name in root
        $this->assertTrue(Location::nameExists('Garage', 1));
        
        // Test existing name in parent
        $this->assertTrue(Location::nameExists('Workbench', 1, 1)); // Workbench under Garage (ID 1)
        
        // Test non-existing name
        $this->assertFalse(Location::nameExists('NonExistent', 1));
        
        // Test name exists for different user
        $this->assertFalse(Location::nameExists('Garage', 2));
        
        // Test name exists excluding current location
        $this->assertFalse(Location::nameExists('Garage', 1, null, 1)); // Exclude Garage with ID 1
    }

    public function testToArray()
    {
        $location = Location::findById(1, 1);
        $array = $location->toArray();
        
        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Garage', $array['name']);
        $this->assertNull($array['parent_id']);
        $this->assertEquals(1, $array['user_id']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }

    public function testLocationValidationWithEmptyName()
    {
        $location = new Location('', null, 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid location data');
        $location->create();
    }

    public function testLocationValidationWithWhitespaceName()
    {
        $location = new Location('   ', null, 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid location data');
        $location->create();
    }

    public function testLocationValidationWithNoUserId()
    {
        $location = new Location('Test Location', null, null);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid location data');
        $location->create();
    }

    public function testLocationValidationWithLongName()
    {
        $longName = str_repeat('a', 101); // 101 characters
        $location = new Location($longName, null, 1);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid location data');
        $location->create();
    }

    public function testLocationSetters()
    {
        $location = new Location();
        
        $location->setName('Test Name');
        $location->setParentId(1);
        $location->setUserId(1);
        
        $this->assertEquals('Test Name', $location->getName());
        $this->assertEquals(1, $location->getParentId());
        $this->assertEquals(1, $location->getUserId());
    }

    public function testLocationGetters()
    {
        $location = new Location('Test Location', 1, 1);
        
        $this->assertEquals('Test Location', $location->getName());
        $this->assertEquals(1, $location->getParentId());
        $this->assertEquals(1, $location->getUserId());
        $this->assertNull($location->getId());
        $this->assertNull($location->getCreatedAt());
        $this->assertNull($location->getUpdatedAt());
    }

    public function testBuildHierarchyWithNestedStructure()
    {
        // Create a more complex hierarchy for testing
        $parentId = $this->createTestLocation('Parent', null, 1);
        $childId = $this->createTestLocation('Child', $parentId, 1);
        $grandchildId = $this->createTestLocation('Grandchild', $childId, 1);
        
        $hierarchy = Location::getHierarchy(1);
        
        $this->assertIsArray($hierarchy);
        // Should have Garage, Storage Room, and Parent as root locations
        $this->assertCount(3, $hierarchy);
    }
}
