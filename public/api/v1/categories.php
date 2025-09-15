<?php
/**
 * Categories API v1
 * RESTful API for categories management
 */

use StorageUnit\Controllers\ApiController;
use StorageUnit\Core\ApiResponse;
use StorageUnit\Models\Category;

class CategoriesApiController extends ApiController
{
    /**
     * GET /api/v1/categories
     * Get all categories for the authenticated user
     */
    public function index()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            $pagination = $this->getPaginationParams();
            
            // Get search parameters
            $search = $_GET['search'] ?? '';
            $sortBy = $_GET['sort_by'] ?? 'name';
            $sortOrder = $_GET['sort_order'] ?? 'asc';
            
            // Validate sort parameters
            $allowedSortFields = ['name', 'created_at', 'updated_at', 'item_count'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'name';
            }
            
            $allowedSortOrders = ['asc', 'desc'];
            if (!in_array($sortOrder, $allowedSortOrders)) {
                $sortOrder = 'asc';
            }
            
            // Get categories with filters
            $categories = Category::getWithItemCount(
                $user->getId(),
                $search,
                $sortBy,
                $sortOrder,
                $pagination['limit'],
                $pagination['offset']
            );
            
            // Get total count for pagination
            $total = Category::getCountForUser($user->getId(), $search);
            
            $this->sendPaginatedResponse($categories, $total, $pagination['page'], $pagination['limit']);
        });
    }

    /**
     * GET /api/v1/categories/{id}
     * Get a specific category
     */
    public function show()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            
            $category = Category::findById($id, $user->getId());
            if (!$category) {
                ApiResponse::notFound('Category not found');
            }
            
            // Get items in this category
            $items = Category::getItemsInCategory($id, $user->getId());
            $category['items'] = $items;
            $category['item_count'] = count($items);
            
            ApiResponse::success($category);
        });
    }

    /**
     * POST /api/v1/categories
     * Create a new category
     */
    public function create()
    {
        $this->handleRequest('POST', function() {
            $user = $this->getCurrentUser();
            $input = $this->getJsonInput();
            
            // Validate required fields
            $this->validateRequiredFields($input, ['name']);
            
            // Validate optional fields
            $name = trim($input['name']);
            $color = $input['color'] ?? '#007bff';
            $icon = $input['icon'] ?? 'fas fa-box';
            
            // Validate name length
            if (strlen($name) > 100) {
                ApiResponse::validationError([
                    'name' => 'Category name must be less than 100 characters'
                ], 'Validation failed');
            }
            
            // Check if name already exists
            if (Category::nameExists($name, $user->getId())) {
                ApiResponse::validationError([
                    'name' => 'Category name already exists'
                ], 'Validation failed');
            }
            
            // Validate color format
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                ApiResponse::validationError([
                    'color' => 'Invalid color format. Use hex format like #007bff'
                ], 'Validation failed');
            }
            
            // Create category
            $category = new Category($name, $color, $icon, $user->getId());
            
            if ($category->create()) {
                ApiResponse::created($category, 'Category created successfully');
            } else {
                ApiResponse::error('Failed to create category', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * PUT /api/v1/categories/{id}
     * Update an existing category
     */
    public function update()
    {
        $this->handleRequest('PUT', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            $input = $this->getJsonInput();
            
            $category = Category::findById($id, $user->getId());
            if (!$category) {
                ApiResponse::notFound('Category not found');
            }
            
            // Validate required fields
            $this->validateRequiredFields($input, ['name']);
            
            $name = trim($input['name']);
            $color = $input['color'] ?? $category->getColor();
            $icon = $input['icon'] ?? $category->getIcon();
            
            // Validate name length
            if (strlen($name) > 100) {
                ApiResponse::validationError([
                    'name' => 'Category name must be less than 100 characters'
                ], 'Validation failed');
            }
            
            // Check if name already exists (excluding current category)
            if (Category::nameExists($name, $user->getId(), $id)) {
                ApiResponse::validationError([
                    'name' => 'Category name already exists'
                ], 'Validation failed');
            }
            
            // Validate color format
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                ApiResponse::validationError([
                    'color' => 'Invalid color format. Use hex format like #007bff'
                ], 'Validation failed');
            }
            
            // Update category
            $category->setName($name);
            $category->setColor($color);
            $category->setIcon($icon);
            
            if ($category->update()) {
                ApiResponse::success($category, 'Category updated successfully');
            } else {
                ApiResponse::error('Failed to update category', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * PATCH /api/v1/categories/{id}
     * Partially update an existing category
     */
    public function patch()
    {
        $this->handleRequest('PATCH', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            $input = $this->getJsonInput();
            
            $category = Category::findById($id, $user->getId());
            if (!$category) {
                ApiResponse::notFound('Category not found');
            }
            
            // Update only provided fields
            if (isset($input['name'])) {
                $name = trim($input['name']);
                
                if (strlen($name) > 100) {
                    ApiResponse::validationError([
                        'name' => 'Category name must be less than 100 characters'
                    ], 'Validation failed');
                }
                
                if (Category::nameExists($name, $user->getId(), $id)) {
                    ApiResponse::validationError([
                        'name' => 'Category name already exists'
                    ], 'Validation failed');
                }
                
                $category->setName($name);
            }
            
            if (isset($input['color'])) {
                $color = $input['color'];
                
                if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                    ApiResponse::validationError([
                        'color' => 'Invalid color format. Use hex format like #007bff'
                    ], 'Validation failed');
                }
                
                $category->setColor($color);
            }
            
            if (isset($input['icon'])) {
                $category->setIcon($input['icon']);
            }
            
            if ($category->update()) {
                ApiResponse::success($category, 'Category updated successfully');
            } else {
                ApiResponse::error('Failed to update category', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * DELETE /api/v1/categories/{id}
     * Delete a category
     */
    public function delete()
    {
        $this->handleRequest('DELETE', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            
            $category = Category::findById($id, $user->getId());
            if (!$category) {
                ApiResponse::notFound('Category not found');
            }
            
            // Check if category has items
            $itemCount = Category::getItemCount($id, $user->getId());
            if ($itemCount > 0) {
                ApiResponse::error(
                    "Cannot delete category with {$itemCount} items. Please move or delete items first.",
                    409,
                    'Conflict'
                );
            }
            
            if ($category->delete()) {
                ApiResponse::noContent();
            } else {
                ApiResponse::error('Failed to delete category', 500, 'Internal Server Error');
            }
        });
    }
}

// Route the request
$controller = new CategoriesApiController();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Determine which method to call based on HTTP method and path
if ($method === 'GET' && count($pathParts) === 4) {
    // GET /api/v1/categories/{id}
    $controller->show();
} elseif ($method === 'GET') {
    // GET /api/v1/categories
    $controller->index();
} elseif ($method === 'POST') {
    // POST /api/v1/categories
    $controller->create();
} elseif ($method === 'PUT' && count($pathParts) === 4) {
    // PUT /api/v1/categories/{id}
    $controller->update();
} elseif ($method === 'PATCH' && count($pathParts) === 4) {
    // PATCH /api/v1/categories/{id}
    $controller->patch();
} elseif ($method === 'DELETE' && count($pathParts) === 4) {
    // DELETE /api/v1/categories/{id}
    $controller->delete();
} else {
    ApiResponse::methodNotAllowed();
}
?>
