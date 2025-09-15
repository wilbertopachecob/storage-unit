<?php
/**
 * Analytics API v1
 * RESTful API for analytics data
 */

use StorageUnit\Controllers\ApiController;
use StorageUnit\Core\ApiResponse;
use StorageUnit\Controllers\AnalyticsController;

class AnalyticsApiController extends ApiController
{
    /**
     * GET /api/v1/analytics
     * Get analytics data for the authenticated user
     */
    public function index()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            
            // Get optional parameters
            $period = $_GET['period'] ?? 'all'; // all, month, year
            $includeCharts = filter_var($_GET['include_charts'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
            $includeRecent = filter_var($_GET['include_recent'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
            
            // Validate period parameter
            $allowedPeriods = ['all', 'month', 'year', 'week', 'day'];
            if (!in_array($period, $allowedPeriods)) {
                $period = 'all';
            }
            
            try {
                $analyticsController = new AnalyticsController();
                $analyticsData = $analyticsController->getAnalyticsData($user->getId());
                
                if (!$analyticsData['success']) {
                    ApiResponse::error($analyticsData['message'], 500, 'Internal Server Error');
                }
                
                // Filter data based on period if needed
                if ($period !== 'all') {
                    $analyticsData['data'] = $this->filterDataByPeriod($analyticsData['data'], $period);
                }
                
                // Remove charts data if not requested
                if (!$includeCharts && isset($analyticsData['data']['monthly_data'])) {
                    unset($analyticsData['data']['monthly_data']);
                }
                
                // Remove recent items if not requested
                if (!$includeRecent && isset($analyticsData['data']['recent_items'])) {
                    unset($analyticsData['data']['recent_items']);
                }
                
                ApiResponse::success($analyticsData['data']);
                
            } catch (Exception $e) {
                ApiResponse::error('Failed to retrieve analytics data', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * GET /api/v1/analytics/summary
     * Get analytics summary data
     */
    public function summary()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            
            try {
                $analyticsController = new AnalyticsController();
                $analyticsData = $analyticsController->getAnalyticsData($user->getId());
                
                if (!$analyticsData['success']) {
                    ApiResponse::error($analyticsData['message'], 500, 'Internal Server Error');
                }
                
                // Extract only summary data
                $summary = [
                    'total_items' => $analyticsData['data']['total_items'] ?? 0,
                    'total_quantity' => $analyticsData['data']['total_quantity'] ?? 0,
                    'total_categories' => $analyticsData['data']['total_categories'] ?? 0,
                    'total_locations' => $analyticsData['data']['total_locations'] ?? 0,
                    'image_coverage' => $analyticsData['data']['image_coverage'] ?? 0,
                    'avg_quantity' => $analyticsData['data']['avg_quantity'] ?? 0
                ];
                
                ApiResponse::success($summary);
                
            } catch (Exception $e) {
                ApiResponse::error('Failed to retrieve analytics summary', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * GET /api/v1/analytics/charts
     * Get analytics chart data
     */
    public function charts()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            
            // Get optional parameters
            $period = $_GET['period'] ?? 'all';
            $chartType = $_GET['chart_type'] ?? 'all'; // all, monthly, category, location
            
            // Validate parameters
            $allowedPeriods = ['all', 'month', 'year', 'week', 'day'];
            if (!in_array($period, $allowedPeriods)) {
                $period = 'all';
            }
            
            $allowedChartTypes = ['all', 'monthly', 'category', 'location'];
            if (!in_array($chartType, $allowedChartTypes)) {
                $chartType = 'all';
            }
            
            try {
                $analyticsController = new AnalyticsController();
                $analyticsData = $analyticsController->getAnalyticsData($user->getId());
                
                if (!$analyticsData['success']) {
                    ApiResponse::error($analyticsData['message'], 500, 'Internal Server Error');
                }
                
                $chartData = [];
                
                // Monthly data
                if ($chartType === 'all' || $chartType === 'monthly') {
                    $chartData['monthly'] = $analyticsData['data']['monthly_data'] ?? [];
                }
                
                // Category data
                if ($chartType === 'all' || $chartType === 'category') {
                    $chartData['categories'] = $analyticsData['data']['categories'] ?? [];
                }
                
                // Location data
                if ($chartType === 'all' || $chartType === 'location') {
                    $chartData['locations'] = $analyticsData['data']['locations'] ?? [];
                }
                
                // Filter by period if needed
                if ($period !== 'all') {
                    $chartData = $this->filterChartDataByPeriod($chartData, $period);
                }
                
                ApiResponse::success($chartData);
                
            } catch (Exception $e) {
                ApiResponse::error('Failed to retrieve chart data', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * Filter data by period
     *
     * @param array $data
     * @param string $period
     * @return array
     */
    private function filterDataByPeriod(array $data, string $period): array
    {
        $now = new DateTime();
        $filteredData = $data;
        
        switch ($period) {
            case 'day':
                $startDate = $now->modify('-1 day');
                break;
            case 'week':
                $startDate = $now->modify('-1 week');
                break;
            case 'month':
                $startDate = $now->modify('-1 month');
                break;
            case 'year':
                $startDate = $now->modify('-1 year');
                break;
            default:
                return $data;
        }
        
        // Filter monthly data
        if (isset($data['monthly_data'])) {
            $filteredMonthly = [];
            foreach ($data['monthly_data'] as $month => $count) {
                $monthDate = new DateTime($month . '-01');
                if ($monthDate >= $startDate) {
                    $filteredMonthly[$month] = $count;
                }
            }
            $filteredData['monthly_data'] = $filteredMonthly;
        }
        
        return $filteredData;
    }

    /**
     * Filter chart data by period
     *
     * @param array $chartData
     * @param string $period
     * @return array
     */
    private function filterChartDataByPeriod(array $chartData, string $period): array
    {
        $now = new DateTime();
        
        switch ($period) {
            case 'day':
                $startDate = $now->modify('-1 day');
                break;
            case 'week':
                $startDate = $now->modify('-1 week');
                break;
            case 'month':
                $startDate = $now->modify('-1 month');
                break;
            case 'year':
                $startDate = $now->modify('-1 year');
                break;
            default:
                return $chartData;
        }
        
        // Filter monthly data
        if (isset($chartData['monthly'])) {
            $filteredMonthly = [];
            foreach ($chartData['monthly'] as $month => $count) {
                $monthDate = new DateTime($month . '-01');
                if ($monthDate >= $startDate) {
                    $filteredMonthly[$month] = $count;
                }
            }
            $chartData['monthly'] = $filteredMonthly;
        }
        
        return $chartData;
    }
}

// Route the request
$controller = new AnalyticsApiController();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Determine which method to call based on path
if (count($pathParts) >= 4 && $pathParts[3] === 'summary') {
    // GET /api/v1/analytics/summary
    $controller->summary();
} elseif (count($pathParts) >= 4 && $pathParts[3] === 'charts') {
    // GET /api/v1/analytics/charts
    $controller->charts();
} elseif ($method === 'GET') {
    // GET /api/v1/analytics
    $controller->index();
} else {
    ApiResponse::methodNotAllowed();
}
?>
