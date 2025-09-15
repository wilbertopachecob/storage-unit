<?php
/**
 * Items API v1
 * RESTful API for items management
 */

use StorageUnit\Controllers\ApiController;
use StorageUnit\Core\ApiResponse;
use StorageUnit\Models\Item;
use StorageUnit\Models\Category;
use StorageUnit\Models\Location;

class ItemsApiController extends ApiController
{
    /**
     * GET /api/v1/items
     * Get all items for the authenticated user
     */
    public function index()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            $pagination = $this->getPaginationParams();
            
            // Get search parameters
            $search = $_GET['search'] ?? '';
            $categoryId = $_GET['category_id'] ?? null;
            $locationId = $_GET['location_id'] ?? null;
            $sortBy = $_GET['sort_by'] ?? 'created_at';
            $sortOrder = $_GET['sort_order'] ?? 'desc';
            
            // Validate sort parameters
            $allowedSortFields = ['title', 'created_at', 'updated_at', 'qty'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'created_at';
            }
            
            $allowedSortOrders = ['asc', 'desc'];
            if (!in_array($sortOrder, $allowedSortOrders)) {
                $sortOrder = 'desc';
            }
            
            // Get items with filters
            $items = Item::getAllWithDetails(
                $user->getId(),
                $search,
                $categoryId,
                $locationId,
                $sortBy,
                $sortOrder,
                $pagination['limit'],
                $pagination['offset']
            );
            
            // Get total count for pagination
            $total = Item::getCountForUser($user->getId(), $search, $categoryId, $locationId);
            
            $this->sendPaginatedResponse($items, $total, $pagination['page'], $pagination['limit']);
        });
    }

    /**
     * GET /api/v1/items/{id}
     * Get a specific item
     */
    public function show()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            
            $item = Item::findById($id, $user->getId());
            if (!$item) {
                ApiResponse::notFound('Item not found');
            }
            
            ApiResponse::success($item);
        });
    }

    /**
     * POST /api/v1/items
     * Create a new item
     */
    public function create()
    {
        $this->handleRequest('POST', function() {
            $user = $this->getCurrentUser();
            $input = $this->getJsonInput();
            
            // Validate required fields
            $this->validateRequiredFields($input, ['title']);
            
            // Validate optional fields
            $qty = max(1, (int)($input['qty'] ?? 1));
            $description = $input['description'] ?? '';
            $img = $input['img'] ?? null;
            $categoryId = $input['category_id'] ?? null;
            $locationId = $input['location_id'] ?? null;
            
            // Validate category and location if provided
            if ($categoryId && !Category::findById($categoryId, $user->getId())) {
                ApiResponse::error('Invalid category ID', 400, 'Bad Request');
            }
            
            if ($locationId && !Location::findById($locationId, $user->getId())) {
                ApiResponse::error('Invalid location ID', 400, 'Bad Request');
            }
            
            // Create item
            $item = new Item(
                $input['title'],
                $description,
                $qty,
                $user->getId(),
                $img,
                $categoryId,
                $locationId
            );
            
            if ($item->create()) {
                ApiResponse::created($item, 'Item created successfully');
            } else {
                ApiResponse::error('Failed to create item', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * PUT /api/v1/items/{id}
     * Update an existing item
     */
    public function update()
    {
        $this->handleRequest('PUT', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            $input = $this->getJsonInput();
            
            $item = Item::findById($id, $user->getId());
            if (!$item) {
                ApiResponse::notFound('Item not found');
            }
            
            // Update fields if provided
            if (isset($input['title'])) {
                $item->setTitle($input['title']);
            }
            
            if (isset($input['description'])) {
                $item->setDescription($input['description']);
            }
            
            if (isset($input['qty'])) {
                $qty = max(1, (int)$input['qty']);
                $item->setQty($qty);
            }
            
            if (isset($input['img'])) {
                $item->setImg($input['img']);
            }
            
            if (isset($input['category_id'])) {
                if ($input['category_id'] && !Category::findById($input['category_id'], $user->getId())) {
                    ApiResponse::error('Invalid category ID', 400, 'Bad Request');
                }
                $item->setCategoryId($input['category_id']);
            }
            
            if (isset($input['location_id'])) {
                if ($input['location_id'] && !Location::findById($input['location_id'], $user->getId())) {
                    ApiResponse::error('Invalid location ID', 400, 'Bad Request');
                }
                $item->setLocationId($input['location_id']);
            }
            
            if ($item->update()) {
                ApiResponse::success($item, 'Item updated successfully');
            } else {
                ApiResponse::error('Failed to update item', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * PATCH /api/v1/items/{id}
     * Partially update an existing item
     */
    public function patch()
    {
        $this->handleRequest('PATCH', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            $input = $this->getJsonInput();
            
            $item = Item::findById($id, $user->getId());
            if (!$item) {
                ApiResponse::notFound('Item not found');
            }
            
            // Update only provided fields
            if (isset($input['title'])) {
                $item->setTitle($input['title']);
            }
            
            if (isset($input['description'])) {
                $item->setDescription($input['description']);
            }
            
            if (isset($input['qty'])) {
                $qty = max(1, (int)$input['qty']);
                $item->setQty($qty);
            }
            
            if (isset($input['img'])) {
                $item->setImg($input['img']);
            }
            
            if (isset($input['category_id'])) {
                if ($input['category_id'] && !Category::findById($input['category_id'], $user->getId())) {
                    ApiResponse::error('Invalid category ID', 400, 'Bad Request');
                }
                $item->setCategoryId($input['category_id']);
            }
            
            if (isset($input['location_id'])) {
                if ($input['location_id'] && !Location::findById($input['location_id'], $user->getId())) {
                    ApiResponse::error('Invalid location ID', 400, 'Bad Request');
                }
                $item->setLocationId($input['location_id']);
            }
            
            if ($item->update()) {
                ApiResponse::success($item, 'Item updated successfully');
            } else {
                ApiResponse::error('Failed to update item', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * DELETE /api/v1/items/{id}
     * Delete an item
     */
    public function delete()
    {
        $this->handleRequest('DELETE', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            
            $item = Item::findById($id, $user->getId());
            if (!$item) {
                ApiResponse::notFound('Item not found');
            }
            
            // Delete image file if exists
            if ($item->getImg() && file_exists(UPLOADS_PATH . '/' . $item->getImg())) {
                unlink(UPLOADS_PATH . '/' . $item->getImg());
            }
            
            if ($item->delete()) {
                ApiResponse::noContent();
            } else {
                ApiResponse::error('Failed to delete item', 500, 'Internal Server Error');
            }
        });
    }
}

// Route the request
$controller = new ItemsApiController();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Determine which method to call based on HTTP method and path
if ($method === 'GET' && count($pathParts) === 4) {
    // GET /api/v1/items/{id}
    $controller->show();
} elseif ($method === 'GET') {
    // GET /api/v1/items
    $controller->index();
} elseif ($method === 'POST') {
    // POST /api/v1/items
    $controller->create();
} elseif ($method === 'PUT' && count($pathParts) === 4) {
    // PUT /api/v1/items/{id}
    $controller->update();
} elseif ($method === 'PATCH' && count($pathParts) === 4) {
    // PATCH /api/v1/items/{id}
    $controller->patch();
} elseif ($method === 'DELETE' && count($pathParts) === 4) {
    // DELETE /api/v1/items/{id}
    $controller->delete();
} else {
    ApiResponse::methodNotAllowed();
}
?>
