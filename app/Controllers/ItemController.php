<?php
/**
 * Item Controller
 * Handles item management operations
 */

namespace StorageUnit\Controllers;

use StorageUnit\Models\Item;
use StorageUnit\Models\User;
use StorageUnit\Core\Security;

class ItemController
{
    /**
     * Get all items for current user
     */
    public function index()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $items = Item::getAllForUser($user->getId());
        
        return [
            'items' => $items,
            'total_quantity' => Item::getTotalQuantityForUser($user->getId()),
            'total_count' => Item::getCountForUser($user->getId())
        ];
    }

    /**
     * Show specific item
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

        return ['item' => $item];
    }

    /**
     * Create new item
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

                // Validate input
                if (empty($title)) {
                    $errors[] = 'Title is required';
                }

                if ($qty < 1) {
                    $errors[] = 'Quantity must be at least 1';
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
                        $item = new Item($title, $description, $qty, $user->getId(), $img);
                        if ($item->create()) {
                            $success = true;
                            // Redirect to items list after successful creation
                            header('Location: ' . BASE_URL . '/index.php?script=itemsList');
                            exit;
                        } else {
                            $errors[] = 'Failed to create item';
                        }
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }
        }

        return [
            'errors' => $errors,
            'success' => $success,
            'csrf_token' => Security::generateCSRFToken()
        ];
    }

    /**
     * Update existing item
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

                // Validate input
                if (empty($title)) {
                    $errors[] = 'Title is required';
                }

                if ($qty < 1) {
                    $errors[] = 'Quantity must be at least 1';
                }

                // Handle file upload
                if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                    $uploadErrors = Security::validateFileUpload($_FILES['img']);
                    if (!empty($uploadErrors)) {
                        $errors = array_merge($errors, $uploadErrors);
                    } else {
                        $img = Security::generateSecureFilename($_FILES['img']['name']);
                        $uploadPath = UPLOADS_PATH . '/' . $img;
                        
                        if (move_uploaded_file($_FILES['img']['tmp_name'], $uploadPath)) {
                            // Delete old image if exists
                            if ($item->getImg() && file_exists(UPLOADS_PATH . '/' . $item->getImg())) {
                                unlink(UPLOADS_PATH . '/' . $item->getImg());
                            }
                            $item->setImg($img);
                        } else {
                            $errors[] = 'Failed to upload image';
                        }
                    }
                }

                if (empty($errors)) {
                    $item->setTitle($title);
                    $item->setDescription($description);
                    $item->setQty($qty);

                    if ($item->update()) {
                        $success = true;
                        // Redirect to items list after successful update
                        header('Location: ' . BASE_URL . '/index.php?script=itemsList');
                        exit;
                    } else {
                        $errors[] = 'Failed to update item';
                    }
                }
            }
        }

        return [
            'item' => $item,
            'errors' => $errors,
            'success' => $success,
            'csrf_token' => Security::generateCSRFToken()
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

        // Delete image file if exists
        if ($item->getImg() && file_exists(UPLOADS_PATH . '/' . $item->getImg())) {
            unlink(UPLOADS_PATH . '/' . $item->getImg());
        }

        if ($item->delete()) {
            header('Location: ' . BASE_URL . '/index.php?script=itemsList');
            exit;
        } else {
            throw new \Exception('Failed to delete item');
        }
    }

    /**
     * Search items
     */
    public function search($query)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $query = Security::sanitizeInput($query);
        $items = Item::searchByTitle($query, $user->getId());

        return [
            'items' => $items,
            'query' => $query,
            'total_count' => count($items)
        ];
    }
}
