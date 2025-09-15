<?php
/**
 * Locations API v1
 * RESTful API for locations management
 */

use StorageUnit\Controllers\ApiController;
use StorageUnit\Core\ApiResponse;
use StorageUnit\Models\Location;

class LocationsApiController extends ApiController
{
    /**
     * GET /api/v1/locations
     * Get all locations for the authenticated user
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
            
            // Get locations with filters
            $locations = Location::getWithItemCount(
                $user->getId(),
                $search,
                $sortBy,
                $sortOrder,
                $pagination['limit'],
                $pagination['offset']
            );
            
            // Get total count for pagination
            $total = Location::getCountForUser($user->getId(), $search);
            
            $this->sendPaginatedResponse($locations, $total, $pagination['page'], $pagination['limit']);
        });
    }

    /**
     * GET /api/v1/locations/{id}
     * Get a specific location
     */
    public function show()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            
            $location = Location::findById($id, $user->getId());
            if (!$location) {
                ApiResponse::notFound('Location not found');
            }
            
            // Get items in this location
            $items = Location::getItemsInLocation($id, $user->getId());
            $location['items'] = $items;
            $location['item_count'] = count($items);
            
            ApiResponse::success($location);
        });
    }

    /**
     * POST /api/v1/locations
     * Create a new location
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
            $description = $input['description'] ?? '';
            $address = $input['address'] ?? '';
            $latitude = $input['latitude'] ?? null;
            $longitude = $input['longitude'] ?? null;
            
            // Validate name length
            if (strlen($name) > 100) {
                ApiResponse::validationError([
                    'name' => 'Location name must be less than 100 characters'
                ], 'Validation failed');
            }
            
            // Check if name already exists
            if (Location::nameExists($name, $user->getId())) {
                ApiResponse::validationError([
                    'name' => 'Location name already exists'
                ], 'Validation failed');
            }
            
            // Validate coordinates if provided
            if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
                ApiResponse::validationError([
                    'latitude' => 'Latitude must be between -90 and 90'
                ], 'Validation failed');
            }
            
            if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
                ApiResponse::validationError([
                    'longitude' => 'Longitude must be between -180 and 180'
                ], 'Validation failed');
            }
            
            // Create location
            $location = new Location($name, $description, $address, $latitude, $longitude, $user->getId());
            
            if ($location->create()) {
                ApiResponse::created($location, 'Location created successfully');
            } else {
                ApiResponse::error('Failed to create location', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * PUT /api/v1/locations/{id}
     * Update an existing location
     */
    public function update()
    {
        $this->handleRequest('PUT', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            $input = $this->getJsonInput();
            
            $location = Location::findById($id, $user->getId());
            if (!$location) {
                ApiResponse::notFound('Location not found');
            }
            
            // Validate required fields
            $this->validateRequiredFields($input, ['name']);
            
            $name = trim($input['name']);
            $description = $input['description'] ?? '';
            $address = $input['address'] ?? '';
            $latitude = $input['latitude'] ?? null;
            $longitude = $input['longitude'] ?? null;
            
            // Validate name length
            if (strlen($name) > 100) {
                ApiResponse::validationError([
                    'name' => 'Location name must be less than 100 characters'
                ], 'Validation failed');
            }
            
            // Check if name already exists (excluding current location)
            if (Location::nameExists($name, $user->getId(), $id)) {
                ApiResponse::validationError([
                    'name' => 'Location name already exists'
                ], 'Validation failed');
            }
            
            // Validate coordinates if provided
            if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
                ApiResponse::validationError([
                    'latitude' => 'Latitude must be between -90 and 90'
                ], 'Validation failed');
            }
            
            if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
                ApiResponse::validationError([
                    'longitude' => 'Longitude must be between -180 and 180'
                ], 'Validation failed');
            }
            
            // Update location
            $location->setName($name);
            $location->setDescription($description);
            $location->setAddress($address);
            $location->setLatitude($latitude);
            $location->setLongitude($longitude);
            
            if ($location->update()) {
                ApiResponse::success($location, 'Location updated successfully');
            } else {
                ApiResponse::error('Failed to update location', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * PATCH /api/v1/locations/{id}
     * Partially update an existing location
     */
    public function patch()
    {
        $this->handleRequest('PATCH', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            $input = $this->getJsonInput();
            
            $location = Location::findById($id, $user->getId());
            if (!$location) {
                ApiResponse::notFound('Location not found');
            }
            
            // Update only provided fields
            if (isset($input['name'])) {
                $name = trim($input['name']);
                
                if (strlen($name) > 100) {
                    ApiResponse::validationError([
                        'name' => 'Location name must be less than 100 characters'
                    ], 'Validation failed');
                }
                
                if (Location::nameExists($name, $user->getId(), $id)) {
                    ApiResponse::validationError([
                        'name' => 'Location name already exists'
                    ], 'Validation failed');
                }
                
                $location->setName($name);
            }
            
            if (isset($input['description'])) {
                $location->setDescription($input['description']);
            }
            
            if (isset($input['address'])) {
                $location->setAddress($input['address']);
            }
            
            if (isset($input['latitude'])) {
                $latitude = $input['latitude'];
                
                if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
                    ApiResponse::validationError([
                        'latitude' => 'Latitude must be between -90 and 90'
                    ], 'Validation failed');
                }
                
                $location->setLatitude($latitude);
            }
            
            if (isset($input['longitude'])) {
                $longitude = $input['longitude'];
                
                if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
                    ApiResponse::validationError([
                        'longitude' => 'Longitude must be between -180 and 180'
                    ], 'Validation failed');
                }
                
                $location->setLongitude($longitude);
            }
            
            if ($location->update()) {
                ApiResponse::success($location, 'Location updated successfully');
            } else {
                ApiResponse::error('Failed to update location', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * DELETE /api/v1/locations/{id}
     * Delete a location
     */
    public function delete()
    {
        $this->handleRequest('DELETE', function() {
            $user = $this->getCurrentUser();
            $id = $this->validateResourceId();
            
            $location = Location::findById($id, $user->getId());
            if (!$location) {
                ApiResponse::notFound('Location not found');
            }
            
            // Check if location has items
            $itemCount = Location::getItemCount($id, $user->getId());
            if ($itemCount > 0) {
                ApiResponse::error(
                    "Cannot delete location with {$itemCount} items. Please move or delete items first.",
                    409,
                    'Conflict'
                );
            }
            
            if ($location->delete()) {
                ApiResponse::noContent();
            } else {
                ApiResponse::error('Failed to delete location', 500, 'Internal Server Error');
            }
        });
    }
}

// Route the request
$controller = new LocationsApiController();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Determine which method to call based on HTTP method and path
if ($method === 'GET' && count($pathParts) === 4) {
    // GET /api/v1/locations/{id}
    $controller->show();
} elseif ($method === 'GET') {
    // GET /api/v1/locations
    $controller->index();
} elseif ($method === 'POST') {
    // POST /api/v1/locations
    $controller->create();
} elseif ($method === 'PUT' && count($pathParts) === 4) {
    // PUT /api/v1/locations/{id}
    $controller->update();
} elseif ($method === 'PATCH' && count($pathParts) === 4) {
    // PATCH /api/v1/locations/{id}
    $controller->patch();
} elseif ($method === 'DELETE' && count($pathParts) === 4) {
    // DELETE /api/v1/locations/{id}
    $controller->delete();
} else {
    ApiResponse::methodNotAllowed();
}
?>
