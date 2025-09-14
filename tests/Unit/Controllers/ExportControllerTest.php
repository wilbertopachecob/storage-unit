<?php

namespace StorageUnit\Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use StorageUnit\Controllers\ExportController;
use StorageUnit\Models\User;
use StorageUnit\Models\Item;
use StorageUnit\Models\Category;
use StorageUnit\Models\Location;

class ExportControllerTest extends TestCase
{
    private $exportController;

    protected function setUp(): void
    {
        $this->exportController = new ExportController();
    }

    public function testExportControllerExists()
    {
        $this->assertInstanceOf(ExportController::class, $this->exportController);
    }

    public function testExportControllerHasRequiredMethods()
    {
        $this->assertTrue(method_exists($this->exportController, 'exportItems'));
        $this->assertTrue(method_exists($this->exportController, 'exportCategories'));
        $this->assertTrue(method_exists($this->exportController, 'exportLocations'));
        $this->assertTrue(method_exists($this->exportController, 'exportItemsByCategory'));
        $this->assertTrue(method_exists($this->exportController, 'exportItemsByLocation'));
        $this->assertTrue(method_exists($this->exportController, 'exportSearchResults'));
    }

    public function testSanitizeFilenameMethod()
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->exportController);
        $method = $reflection->getMethod('sanitizeFilename');
        $method->setAccessible(true);

        $this->assertEquals('test_file', $method->invoke($this->exportController, 'test file'));
        $this->assertEquals('test_file', $method->invoke($this->exportController, 'test@#$file'));
        $this->assertEquals('export', $method->invoke($this->exportController, ''));
        $this->assertEquals('export', $method->invoke($this->exportController, '   '));
    }

    public function testGetLocationPathMethod()
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->exportController);
        $method = $reflection->getMethod('getLocationPath');
        $method->setAccessible(true);

        $location = ['id' => 1, 'name' => 'Garage', 'parent_id' => null];
        $allLocations = [$location];

        $result = $method->invoke($this->exportController, $location, $allLocations);
        $this->assertEquals('Garage', $result);

        // Test with parent location
        $parentLocation = ['id' => 2, 'name' => 'House', 'parent_id' => null];
        $childLocation = ['id' => 1, 'name' => 'Garage', 'parent_id' => 2];
        $allLocations = [$parentLocation, $childLocation];

        $result = $method->invoke($this->exportController, $childLocation, $allLocations);
        $this->assertEquals('House → Garage', $result);
    }
}
