<?php

namespace Tests\Unit\Services;

use StorageUnit\Tests\TestCase;
use StorageUnit\Services\AnalyticsService;

class AnalyticsServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AnalyticsService();
    }

    /**
     * Test calculateTimeBasedAnalytics returns correct structure
     */
    public function testCalculateTimeBasedAnalyticsReturnsCorrectStructure()
    {
        $items = [
            ['created_at' => '2024-01-15 10:00:00'],
            ['created_at' => '2024-01-20 14:30:00'],
            ['created_at' => '2024-02-10 09:15:00'],
            ['created_at' => '2024-02-15 16:45:00'],
            ['created_at' => '2024-03-05 11:20:00']
        ];

        $result = $this->service->calculateTimeBasedAnalytics($items);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('monthly_data', $result);
        $this->assertArrayHasKey('weekly_data', $result);
        $this->assertArrayHasKey('daily_data', $result);
        $this->assertArrayHasKey('yearly_data', $result);

        // Test monthly data
        $this->assertArrayHasKey('2024-01', $result['monthly_data']);
        $this->assertArrayHasKey('2024-02', $result['monthly_data']);
        $this->assertArrayHasKey('2024-03', $result['monthly_data']);
        $this->assertEquals(2, $result['monthly_data']['2024-01']);
        $this->assertEquals(2, $result['monthly_data']['2024-02']);
        $this->assertEquals(1, $result['monthly_data']['2024-03']);
    }

    /**
     * Test calculateCategoryAnalytics returns correct structure
     */
    public function testCalculateCategoryAnalyticsReturnsCorrectStructure()
    {
        $items = [
            ['category_id' => 1, 'qty' => 2],
            ['category_id' => 1, 'qty' => 3],
            ['category_id' => 2, 'qty' => 1],
            ['category_id' => 2, 'qty' => 4]
        ];

        $categories = [
            ['id' => 1, 'name' => 'Tools', 'color' => '#ff0000'],
            ['id' => 2, 'name' => 'Electronics', 'color' => '#00ff00']
        ];

        $result = $this->service->calculateCategoryAnalytics($items, $categories);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        // Test first category
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('Tools', $result[0]['name']);
        $this->assertEquals('#ff0000', $result[0]['color']);
        $this->assertEquals(2, $result[0]['count']);
        $this->assertEquals(5, $result[0]['total_quantity']);
        $this->assertEquals(2.5, $result[0]['avg_quantity']);

        // Test second category
        $this->assertEquals(2, $result[1]['id']);
        $this->assertEquals('Electronics', $result[1]['name']);
        $this->assertEquals('#00ff00', $result[1]['color']);
        $this->assertEquals(2, $result[1]['count']);
        $this->assertEquals(5, $result[1]['total_quantity']);
        $this->assertEquals(2.5, $result[1]['avg_quantity']);
    }

    /**
     * Test calculateLocationAnalytics returns correct structure
     */
    public function testCalculateLocationAnalyticsReturnsCorrectStructure()
    {
        $items = [
            ['location_id' => 1, 'qty' => 2],
            ['location_id' => 1, 'qty' => 3],
            ['location_id' => 2, 'qty' => 1]
        ];

        $locations = [
            ['id' => 1, 'name' => 'Garage'],
            ['id' => 2, 'name' => 'Basement']
        ];

        $result = $this->service->calculateLocationAnalytics($items, $locations);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        // Test first location
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('Garage', $result[0]['name']);
        $this->assertEquals(2, $result[0]['count']);
        $this->assertEquals(5, $result[0]['total_quantity']);

        // Test second location
        $this->assertEquals(2, $result[1]['id']);
        $this->assertEquals('Basement', $result[1]['name']);
        $this->assertEquals(1, $result[1]['count']);
        $this->assertEquals(1, $result[1]['total_quantity']);
    }

    /**
     * Test calculateImageStatistics returns correct structure
     */
    public function testCalculateImageStatisticsReturnsCorrectStructure()
    {
        $items = [
            ['img' => 'image1.jpg'],
            ['img' => 'image2.jpg'],
            ['img' => ''],
            ['img' => null],
            ['img' => 'image3.jpg']
        ];

        $result = $this->service->calculateImageStatistics($items);

        $this->assertIsArray($result);
        $this->assertEquals(5, $result['total_items']);
        $this->assertEquals(3, $result['items_with_images']);
        $this->assertEquals(2, $result['items_without_images']);
        $this->assertEquals(60.0, $result['image_coverage_percentage']);
        $this->assertEquals(0.6, $result['image_coverage_ratio']);
    }

    /**
     * Test calculateQuantityStatistics returns correct structure
     */
    public function testCalculateQuantityStatisticsReturnsCorrectStructure()
    {
        $items = [
            ['qty' => 1],
            ['qty' => 3],
            ['qty' => 5],
            ['qty' => 7],
            ['qty' => 9]
        ];

        $result = $this->service->calculateQuantityStatistics($items);

        $this->assertIsArray($result);
        $this->assertEquals(25, $result['total_quantity']);
        $this->assertEquals(5.0, $result['average_quantity']);
        $this->assertEquals(1, $result['min_quantity']);
        $this->assertEquals(9, $result['max_quantity']);
        $this->assertEquals(5.0, $result['median_quantity']);
    }

    /**
     * Test calculateQuantityStatistics with empty array
     */
    public function testCalculateQuantityStatisticsWithEmptyArray()
    {
        $result = $this->service->calculateQuantityStatistics([]);

        $this->assertEquals(0, $result['total_quantity']);
        $this->assertEquals(0, $result['average_quantity']);
        $this->assertEquals(0, $result['min_quantity']);
        $this->assertEquals(0, $result['max_quantity']);
        $this->assertEquals(0, $result['median_quantity']);
    }

    /**
     * Test calculateImageStatistics with empty array
     */
    public function testCalculateImageStatisticsWithEmptyArray()
    {
        $result = $this->service->calculateImageStatistics([]);

        $this->assertEquals(0, $result['total_items']);
        $this->assertEquals(0, $result['items_with_images']);
        $this->assertEquals(0, $result['items_without_images']);
        $this->assertEquals(0, $result['image_coverage_percentage']);
        $this->assertEquals(0, $result['image_coverage_ratio']);
    }

    /**
     * Test time-based analytics with different date formats
     */
    public function testTimeBasedAnalyticsWithDifferentDateFormats()
    {
        $items = [
            ['created_at' => '2024-01-01 00:00:00'],
            ['created_at' => '2024-01-31 23:59:59'],
            ['created_at' => '2024-02-15 12:30:00']
        ];

        $result = $this->service->calculateTimeBasedAnalytics($items);

        $this->assertEquals(2, $result['monthly_data']['2024-01']);
        $this->assertEquals(1, $result['monthly_data']['2024-02']);
    }
}
