<?php
/**
 * Enhanced Item Controller
 * Handles item management operations with categories and locations
 */

namespace StorageUnit\Controllers;

use StorageUnit\Models\Item;
use StorageUnit\Models\User;
use StorageUnit\Models\Category;
use StorageUnit\Models\Location;
use StorageUnit\Core\Security;

class EnhancedItemController
{
    /**
     * Get all items for current user with enhanced data
     */
    public function index()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $items = Item::getAllWithDetails($user->getId());
        $categories = Category::getAllForUser($user->getId());
        $locations = Location::getAllForUser($user->getId());
        
        return [
            'items' => $items,
            'categories' => $categories,
            'locations' => $locations,
            'total_quantity' => Item::getTotalQuantityForUser($user->getId()),
            'total_count' => Item::getCountForUser($user->getId())
        ];
    }

    /**
     * Show specific item with enhanced data
     */
    public function show($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $item = Item::findById($id, $user->getId());
        if (!$item) {
            throw new \Exception('Item not found');
        }

        // Get category and location details
        $category = $item->getCategoryId() ? Category::findById($item->getCategoryId(), $user->getId()) : null;
        $location = $item->getLocationId() ? Location::findById($item->getLocationId(), $user->getId()) : null;

        return [
            'item' => $item,
            'category' => $category,
            'location' => $location
        ];
    }

    /**
     * Create new item with category and location support
     */
    public function create()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid security token';
            } else {
                // Sanitize input
                $title = Security::sanitizeInput($_POST['title'] ?? '');
                $description = Security::sanitizeInput($_POST['description'] ?? '');
                $qty = (int)($_POST['qty'] ?? 1);
                $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
                $locationId = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;

                // Validate input
                if (empty($title)) {
                    $errors[] = 'Title is required';
                }

                if ($qty < 1) {
                    $errors[] = 'Quantity must be at least 1';
                }

                // Validate category exists and belongs to user
                if ($categoryId) {
                    $category = Category::findById($categoryId, $user->getId());
                    if (!$category) {
                        $errors[] = 'Selected category not found';
                    }
                }

                // Validate location exists and belongs to user
                if ($locationId) {
                    $location = Location::findById($locationId, $user->getId());
                    if (!$location) {
                        $errors[] = 'Selected location not found';
                    }
                }

                // Handle file upload
                $img = null;
                if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                    $uploadErrors = Security::validateFileUpload($_FILES['img']);
                    if (!empty($uploadErrors)) {
                        $errors = array_merge($errors, $uploadErrors);
                    } else {
                        $img = Security::generateSecureFilename($_FILES['img']['name']);
                        $uploadPath = UPLOADS_PATH . '/' . $img;
                        
                        if (!move_uploaded_file($_FILES['img']['tmp_name'], $uploadPath)) {
                            $errors[] = 'Failed to upload image';
                        }
                    }
                }

                if (empty($errors)) {
                    try {
                        $item = new Item($title, $description, $qty, $user->getId(), $img, $categoryId, $locationId);
                        if ($item->create()) {
                            $success = true;
                            $message = 'Item created successfully';
                        } else {
                            $errors[] = 'Failed to create item';
                        }
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }
        }

        // Get categories and locations for form
        $categories = Category::getAllForUser($user->getId());
        $locations = Location::getAllForUser($user->getId());

        return [
            'categories' => $categories,
            'locations' => $locations,
            'errors' => $errors,
            'success' => $success,
            'message' => $message ?? null
        ];
    }

    /**
     * Update item with category and location support
     */
    public function update($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $item = Item::findById($id, $user->getId());
        if (!$item) {
            throw new \Exception('Item not found');
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid security token';
            } else {
                // Sanitize input
                $title = Security::sanitizeInput($_POST['title'] ?? '');
                $description = Security::sanitizeInput($_POST['description'] ?? '');
                $qty = (int)($_POST['qty'] ?? 1);
                $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
                $locationId = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;

                // Validate input
                if (empty($title)) {
                    $errors[] = 'Title is required';
                }

                if ($qty < 1) {
                    $errors[] = 'Quantity must be at least 1';
                }

                // Validate category exists and belongs to user
                if ($categoryId) {
                    $category = Category::findById($categoryId, $user->getId());
                    if (!$category) {
                        $errors[] = 'Selected category not found';
                    }
                }

                // Validate location exists and belongs to user
                if ($locationId) {
                    $location = Location::findById($locationId, $user->getId());
                    if (!$location) {
                        $errors[] = 'Selected location not found';
                    }
                }

                // Handle file upload
                $img = $item->getImg(); // Keep existing image by default
                if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                    $uploadErrors = Security::validateFileUpload($_FILES['img']);
                    if (!empty($uploadErrors)) {
                        $errors = array_merge($errors, $uploadErrors);
                    } else {
                        $img = Security::generateSecureFilename($_FILES['img']['name']);
                        $uploadPath = UPLOADS_PATH . '/' . $img;
                        
                        if (!move_uploaded_file($_FILES['img']['tmp_name'], $uploadPath)) {
                            $errors[] = 'Failed to upload image';
                        }
                    }
                }

                if (empty($errors)) {
                    try {
                        $item->setTitle($title);
                        $item->setDescription($description);
                        $item->setQty($qty);
                        $item->setCategoryId($categoryId);
                        $item->setLocationId($locationId);
                        $item->setImg($img);

                        if ($item->update()) {
                            $success = true;
                            $message = 'Item updated successfully';
                        } else {
                            $errors[] = 'Failed to update item';
                        }
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }
        }

        // Get categories and locations for form
        $categories = Category::getAllForUser($user->getId());
        $locations = Location::getAllForUser($user->getId());

        return [
            'item' => $item,
            'categories' => $categories,
            'locations' => $locations,
            'errors' => $errors,
            'success' => $success,
            'message' => $message ?? null
        ];
    }

    /**
     * Delete item
     */
    public function delete($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $item = Item::findById($id, $user->getId());
        if (!$item) {
            throw new \Exception('Item not found');
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid security token';
            } else {
                try {
                    if ($item->delete()) {
                        $success = true;
                        $message = 'Item deleted successfully';
                    } else {
                        $errors[] = 'Failed to delete item';
                    }
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }

        return [
            'errors' => $errors,
            'success' => $success,
            'message' => $message ?? null
        ];
    }

    /**
     * Enhanced search with filters
     */
    public function search()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $searchTerm = $_GET['q'] ?? '';
        $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $locationId = !empty($_GET['location_id']) ? (int)$_GET['location_id'] : null;
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : null;
        $offset = !empty($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $items = Item::search($searchTerm, $user->getId(), $categoryId, $locationId, $limit, $offset);
        $categories = Category::getAllForUser($user->getId());
        $locations = Location::getAllForUser($user->getId());

        return [
            'items' => $items,
            'categories' => $categories,
            'locations' => $locations,
            'search_term' => $searchTerm,
            'selected_category' => $categoryId,
            'selected_location' => $locationId,
            'total_count' => count($items)
        ];
    }

    /**
     * Get analytics data
     */
    public function analytics()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $items = Item::getAllWithDetails($user->getId());
        $categories = Category::getWithItemCount($user->getId());
        $locations = Location::getWithItemCount($user->getId());

        // Calculate statistics
        $totalItems = count($items);
        $totalQuantity = array_sum(array_column($items, 'qty'));
        
        // Items by category
        $itemsByCategory = [];
        foreach ($categories as $category) {
            $itemsByCategory[] = [
                'name' => $category['name'],
                'color' => $category['color'],
                'count' => $category['item_count']
            ];
        }

        // Items by location
        $itemsByLocation = [];
        foreach ($locations as $location) {
            $itemsByLocation[] = [
                'name' => $location['name'],
                'count' => $location['item_count']
            ];
        }

        return [
            'total_items' => $totalItems,
            'total_quantity' => $totalQuantity,
            'items_by_category' => $itemsByCategory,
            'items_by_location' => $itemsByLocation,
            'recent_items' => array_slice($items, 0, 5) // Last 5 items
        ];
    }

    /**
     * Export all items to CSV
     */
    public function exportAll()
    {
        $exportController = new \StorageUnit\Controllers\ExportController();
        return $exportController->exportItems();
    }

    /**
     * Export search results to CSV
     */
    public function exportSearch()
    {
        $exportController = new \StorageUnit\Controllers\ExportController();
        return $exportController->exportSearchResults();
    }
}
