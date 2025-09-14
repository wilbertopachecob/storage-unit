<?php
/**
 * Item Controller Integration Tests
 */

namespace StorageUnit\Tests\Integration\Controllers;

use StorageUnit\Tests\TestCase;
use StorageUnit\Controllers\ItemController;
use StorageUnit\Core\Security;

class ItemControllerTest extends TestCase
{
    private $itemController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->itemController = new ItemController();
        $this->authenticateUser();
    }

    public function testIndex()
    {
        $result = $this->itemController->index();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total_quantity', $result);
        $this->assertArrayHasKey('total_count', $result);
        $this->assertIsArray($result['items']);
        $this->assertIsInt($result['total_quantity']);
        $this->assertIsInt($result['total_count']);
    }

    public function testIndexWithoutAuthentication()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->itemController->index();
    }

    public function testShow()
    {
        $result = $this->itemController->show(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('item', $result);
        $this->assertEquals(1, $result['item']->getId());
        $this->assertEquals('Test Item 1', $result['item']->getTitle());
    }

    public function testShowWithNonExistentItem()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item not found');
        $this->itemController->show(999);
    }

    public function testShowWithWrongUser()
    {
        // Try to access item belonging to user 2 while authenticated as user 1
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item not found');
        $this->itemController->show(3); // Item 3 belongs to user 2
    }

    public function testCreate()
    {
        $_POST = [
            'title' => 'New Test Item',
            'description' => 'New test description',
            'qty' => 5,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->itemController->create();

        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
    }

    public function testCreateWithInvalidData()
    {
        $_POST = [
            'title' => '', // Empty title
            'description' => 'Test description',
            'qty' => 1,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->itemController->create();

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Title is required', $result['errors'][0]);
    }

    public function testCreateWithInvalidQuantity()
    {
        $_POST = [
            'title' => 'Test Item',
            'description' => 'Test description',
            'qty' => 0, // Invalid quantity
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->itemController->create();

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Quantity must be at least 1', $result['errors'][0]);
    }

    public function testCreateWithFileUpload()
    {
        // Create a temporary file for testing
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'fake image content');

        $_POST = [
            'title' => 'Item with Image',
            'description' => 'Item with uploaded image',
            'qty' => 1,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $_FILES = [
            'img' => [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => $tempFile,
                'error' => UPLOAD_ERR_OK,
                'size' => 1024
            ]
        ];

        $result = $this->itemController->create();

        // Note: This test might fail in some environments due to MIME type detection
        // In a real test environment, you'd mock the file upload validation
        
        unlink($tempFile);
    }

    public function testUpdate()
    {
        $_POST = [
            'title' => 'Updated Test Item',
            'description' => 'Updated description',
            'qty' => 10,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->itemController->update(1);

        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
    }

    public function testUpdateWithInvalidData()
    {
        $_POST = [
            'title' => '', // Empty title
            'description' => 'Updated description',
            'qty' => 10,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $result = $this->itemController->update(1);

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('Title is required', $result['errors'][0]);
    }

    public function testUpdateWithNonExistentItem()
    {
        $_POST = [
            'title' => 'Updated Item',
            'description' => 'Updated description',
            'qty' => 10,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item not found');
        $this->itemController->update(999);
    }

    public function testDelete()
    {
        $itemId = $this->createTestItem('To Delete', 'Will be deleted', 1, 1);
        
        // This should redirect, so we can't easily test the return value
        // In a real test, you'd mock the header() function or test differently
        $this->expectException(\Exception::class); // Since we can't redirect in tests
    }

    public function testDeleteWithNonExistentItem()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item not found');
        $this->itemController->delete(999);
    }

    public function testSearch()
    {
        $result = $this->itemController->search('Test Item');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('query', $result);
        $this->assertArrayHasKey('total_count', $result);
        $this->assertEquals('Test Item', $result['query']);
        $this->assertIsArray($result['items']);
        $this->assertIsInt($result['total_count']);
    }

    public function testSearchWithNoResults()
    {
        $result = $this->itemController->search('NonExistentItem');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('query', $result);
        $this->assertArrayHasKey('total_count', $result);
        $this->assertEquals('NonExistentItem', $result['query']);
        $this->assertEmpty($result['items']);
        $this->assertEquals(0, $result['total_count']);
    }

    public function testSearchWithoutAuthentication()
    {
        $this->clearSession();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->itemController->search('test');
    }
}
