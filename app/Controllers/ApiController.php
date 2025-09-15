<?php
/**
 * Base API Controller
 * Provides common functionality for all API controllers
 */

namespace StorageUnit\Controllers;

use StorageUnit\Core\ApiResponse;
use StorageUnit\Models\User;

abstract class ApiController
{
    /**
     * Get the current authenticated user
     *
     * @return User
     * @throws \Exception
     */
    protected function getCurrentUser(): User
    {
        $user = User::getCurrentUser();
        if (!$user) {
            ApiResponse::unauthorized('User not authenticated');
        }
        return $user;
    }

    /**
     * Validate JSON input
     *
     * @return array
     * @throws \Exception
     */
    protected function getJsonInput(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ApiResponse::error('Invalid JSON input', 400, 'Bad Request');
        }
        
        return $input ?? [];
    }

    /**
     * Validate required fields
     *
     * @param array $data
     * @param array $requiredFields
     * @return void
     */
    protected function validateRequiredFields(array $data, array $requiredFields): void
    {
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            ApiResponse::validationError([
                'missing_fields' => $missingFields
            ], 'Required fields are missing: ' . implode(', ', $missingFields));
        }
    }

    /**
     * Handle different HTTP methods
     *
     * @param string $method
     * @param callable $handler
     * @return void
     */
    protected function handleRequest(string $method, callable $handler): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            ApiResponse::methodNotAllowed("Method {$_SERVER['REQUEST_METHOD']} not allowed for this endpoint");
        }
        
        $handler();
    }

    /**
     * Get pagination parameters
     *
     * @return array
     */
    protected function getPaginationParams(): array
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int)($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Send paginated response
     *
     * @param array $data
     * @param int $total
     * @param int $page
     * @param int $limit
     * @return void
     */
    protected function sendPaginatedResponse(array $data, int $total, int $page, int $limit): void
    {
        $totalPages = ceil($total / $limit);
        
        ApiResponse::success([
            'items' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ]);
    }

    /**
     * Get resource ID from URL
     *
     * @return int|null
     */
    protected function getResourceId(): ?int
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = parse_url($requestUri, PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));
        
        // Get the ID from the path (assuming format: /api/v1/resource/id)
        $id = $pathParts[3] ?? null;
        
        return $id ? (int)$id : null;
    }

    /**
     * Validate resource ID
     *
     * @return int
     */
    protected function validateResourceId(): int
    {
        $id = $this->getResourceId();
        
        if (!$id || $id <= 0) {
            ApiResponse::error('Invalid resource ID', 400, 'Bad Request');
        }
        
        return $id;
    }
}
?>
