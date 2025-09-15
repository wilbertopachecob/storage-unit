<?php

namespace StorageUnit\Services;

use StorageUnit\Models\Item;
use StorageUnit\Models\Category;
use StorageUnit\Models\Location;

/**
 * Analytics Service
 * Handles complex analytics calculations and data processing
 */
class AnalyticsService
{
    /**
     * Calculate time-based analytics data
     *
     * @param array $items
     * @return array
     */
    public function calculateTimeBasedAnalytics(array $items): array
    {
        return [
            'monthly_data' => $this->getMonthlyData($items),
            'weekly_data' => $this->getWeeklyData($items),
            'daily_data' => $this->getDailyData($items),
            'yearly_data' => $this->getYearlyData($items)
        ];
    }

    /**
     * Calculate category analytics
     *
     * @param array $items
     * @param array $categories
     * @return array
     */
    public function calculateCategoryAnalytics(array $items, array $categories): array
    {
        $categoryStats = [];
        
        foreach ($categories as $category) {
            $categoryItems = array_filter($items, function($item) use ($category) {
                return $item['category_id'] == $category['id'];
            });

            $categoryStats[] = [
                'id' => $category['id'],
                'name' => $category['name'],
                'color' => $category['color'],
                'count' => count($categoryItems),
                'total_quantity' => array_sum(array_column($categoryItems, 'qty')),
                'avg_quantity' => count($categoryItems) > 0 
                    ? round(array_sum(array_column($categoryItems, 'qty')) / count($categoryItems), 1) 
                    : 0
            ];
        }

        return $categoryStats;
    }

    /**
     * Calculate location analytics
     *
     * @param array $items
     * @param array $locations
     * @return array
     */
    public function calculateLocationAnalytics(array $items, array $locations): array
    {
        $locationStats = [];
        
        foreach ($locations as $location) {
            $locationItems = array_filter($items, function($item) use ($location) {
                return $item['location_id'] == $location['id'];
            });

            $locationStats[] = [
                'id' => $location['id'],
                'name' => $location['name'],
                'count' => count($locationItems),
                'total_quantity' => array_sum(array_column($locationItems, 'qty')),
                'utilization' => $this->calculateLocationUtilization($location, $locationItems)
            ];
        }

        return $locationStats;
    }

    /**
     * Calculate image statistics
     *
     * @param array $items
     * @return array
     */
    public function calculateImageStatistics(array $items): array
    {
        $itemsWithImages = array_filter($items, function($item) {
            return !empty($item['img']);
        });

        $itemsWithoutImages = array_filter($items, function($item) {
            return empty($item['img']);
        });

        return [
            'total_items' => count($items),
            'items_with_images' => count($itemsWithImages),
            'items_without_images' => count($itemsWithoutImages),
            'image_coverage_percentage' => count($items) > 0 
                ? round((count($itemsWithImages) / count($items)) * 100, 1) 
                : 0,
            'image_coverage_ratio' => count($items) > 0 
                ? round(count($itemsWithImages) / count($items), 2) 
                : 0
        ];
    }

    /**
     * Calculate quantity statistics
     *
     * @param array $items
     * @return array
     */
    public function calculateQuantityStatistics(array $items): array
    {
        $quantities = array_column($items, 'qty');
        
        return [
            'total_quantity' => array_sum($quantities),
            'average_quantity' => count($quantities) > 0 ? round(array_sum($quantities) / count($quantities), 1) : 0,
            'min_quantity' => count($quantities) > 0 ? min($quantities) : 0,
            'max_quantity' => count($quantities) > 0 ? max($quantities) : 0,
            'median_quantity' => $this->calculateMedian($quantities)
        ];
    }

    /**
     * Get monthly data for charts
     *
     * @param array $items
     * @return array
     */
    private function getMonthlyData(array $items): array
    {
        $monthlyData = [];
        
        foreach ($items as $item) {
            $month = date('Y-m', strtotime($item['created_at']));
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = 0;
            }
            $monthlyData[$month]++;
        }
        
        ksort($monthlyData);
        return $monthlyData;
    }

    /**
     * Get weekly data for charts
     *
     * @param array $items
     * @return array
     */
    private function getWeeklyData(array $items): array
    {
        $weeklyData = [];
        
        foreach ($items as $item) {
            $week = date('Y-W', strtotime($item['created_at']));
            if (!isset($weeklyData[$week])) {
                $weeklyData[$week] = 0;
            }
            $weeklyData[$week]++;
        }
        
        ksort($weeklyData);
        return $weeklyData;
    }

    /**
     * Get daily data for charts
     *
     * @param array $items
     * @return array
     */
    private function getDailyData(array $items): array
    {
        $dailyData = [];
        
        foreach ($items as $item) {
            $day = date('Y-m-d', strtotime($item['created_at']));
            if (!isset($dailyData[$day])) {
                $dailyData[$day] = 0;
            }
            $dailyData[$day]++;
        }
        
        ksort($dailyData);
        return $dailyData;
    }

    /**
     * Get yearly data for charts
     *
     * @param array $items
     * @return array
     */
    private function getYearlyData(array $items): array
    {
        $yearlyData = [];
        
        foreach ($items as $item) {
            $year = date('Y', strtotime($item['created_at']));
            if (!isset($yearlyData[$year])) {
                $yearlyData[$year] = 0;
            }
            $yearlyData[$year]++;
        }
        
        ksort($yearlyData);
        return $yearlyData;
    }

    /**
     * Calculate location utilization percentage
     *
     * @param array $location
     * @param array $items
     * @return float
     */
    private function calculateLocationUtilization(array $location, array $items): float
    {
        // This is a placeholder - in a real implementation, you might have
        // capacity limits or other metrics to calculate utilization
        $totalQuantity = array_sum(array_column($items, 'qty'));
        return round($totalQuantity, 1);
    }

    /**
     * Calculate median value
     *
     * @param array $values
     * @return float
     */
    private function calculateMedian(array $values): float
    {
        if (empty($values)) {
            return 0;
        }

        sort($values);
        $count = count($values);
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return round(($values[$middle - 1] + $values[$middle]) / 2, 1);
        } else {
            return round($values[$middle], 1);
        }
    }
}
