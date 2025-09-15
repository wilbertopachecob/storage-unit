<?php

namespace StorageUnit\Controllers;

use StorageUnit\Models\User;
use StorageUnit\Models\Item;
use StorageUnit\Models\Category;
use StorageUnit\Models\Location;

/**
 * Analytics Controller
 * Handles all analytics-related operations and data processing
 */
class AnalyticsController
{
    /**
     * Get comprehensive analytics data for a user
     *
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function getAnalyticsData(int $userId): array
    {
        // Validate user exists
        $user = User::findById($userId);
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Get base analytics data
        $enhancedController = new EnhancedItemController();
        $baseAnalytics = $enhancedController->analytics();

        // Get additional data
        $items = Item::getAllWithDetails($userId);
        $categories = Category::getWithItemCount($userId);
        $locations = Location::getWithItemCount($userId);

        // Calculate enhanced statistics
        $enhancedStats = $this->calculateEnhancedStatistics($items);

        // Prepare comprehensive response
        return [
            'success' => true,
            'data' => array_merge($baseAnalytics, $enhancedStats)
        ];
    }

    /**
     * Calculate enhanced statistics for analytics
     *
     * @param array $items
     * @return array
     */
    private function calculateEnhancedStatistics(array $items): array
    {
        // Calculate image statistics
        $itemsWithoutImages = array_filter($items, function($item) {
            return empty($item['img']);
        });

        $imageStats = [
            'items_without_images' => count($itemsWithoutImages),
            'items_with_images' => count($items) - count($itemsWithoutImages),
            'image_coverage' => count($items) > 0 
                ? round((count($items) - count($itemsWithoutImages)) / count($items) * 100, 1) 
                : 0
        ];

        // Calculate monthly data for time series charts
        $monthlyData = $this->calculateMonthlyData($items);

        // Calculate quantity statistics
        $totalQuantity = array_sum(array_column($items, 'qty'));
        $quantityStats = [
            'avg_quantity' => count($items) > 0 ? round($totalQuantity / count($items), 1) : 0
        ];

        return array_merge($imageStats, [
            'monthly_data' => $monthlyData,
            'recent_items' => array_slice($items, 0, 5)
        ], $quantityStats);
    }

    /**
     * Calculate monthly data for time series charts
     *
     * @param array $items
     * @return array
     */
    private function calculateMonthlyData(array $items): array
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
     * Get analytics data for API response
     *
     * @param int $userId
     * @return array
     */
    public function getApiResponse(int $userId): array
    {
        try {
            return $this->getAnalyticsData($userId);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate user authentication for analytics access
     *
     * @return User
     * @throws \Exception
     */
    public function validateUser(): User
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
        return $user;
    }
}
