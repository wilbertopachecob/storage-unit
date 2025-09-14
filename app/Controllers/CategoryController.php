<?php
/**
 * Category Controller
 * Handles category management operations
 */

namespace StorageUnit\Controllers;

use StorageUnit\Models\Category;
use StorageUnit\Models\User;
use StorageUnit\Core\Security;

class CategoryController
{
    /**
     * Get all categories for current user
     */
    public function index()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $categories = Category::getWithItemCount($user->getId());
        
        return [
            'categories' => $categories,
            'total_count' => count($categories)
        ];
    }

    /**
     * Show specific category
     */
    public function show($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $category = Category::findById($id, $user->getId());
        if (!$category) {
            throw new \Exception('Category not found');
        }

        return ['category' => $category];
    }

    /**
     * Create new category
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
                $name = Security::sanitizeInput($_POST['name'] ?? '');
                $color = Security::sanitizeInput($_POST['color'] ?? '#007bff');
                $icon = Security::sanitizeInput($_POST['icon'] ?? 'fas fa-box');

                // Validate input
                if (empty($name)) {
                    $errors[] = 'Category name is required';
                }

                if (strlen($name) > 100) {
                    $errors[] = 'Category name must be less than 100 characters';
                }

                // Check if name already exists
                if (Category::nameExists($name, $user->getId())) {
                    $errors[] = 'Category name already exists';
                }

                if (empty($errors)) {
                    $category = new Category($name, $color, $icon, $user->getId());
                    
                    if ($category->create()) {
                        $success = true;
                        $message = 'Category created successfully';
                    } else {
                        $errors[] = 'Failed to create category';
                    }
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
     * Update category
     */
    public function update($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $category = Category::findById($id, $user->getId());
        if (!$category) {
            throw new \Exception('Category not found');
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid security token';
            } else {
                // Sanitize input
                $name = Security::sanitizeInput($_POST['name'] ?? '');
                $color = Security::sanitizeInput($_POST['color'] ?? '#007bff');
                $icon = Security::sanitizeInput($_POST['icon'] ?? 'fas fa-box');

                // Validate input
                if (empty($name)) {
                    $errors[] = 'Category name is required';
                }

                if (strlen($name) > 100) {
                    $errors[] = 'Category name must be less than 100 characters';
                }

                // Check if name already exists (excluding current category)
                if (Category::nameExists($name, $user->getId(), $id)) {
                    $errors[] = 'Category name already exists';
                }

                if (empty($errors)) {
                    $category->setName($name);
                    $category->setColor($color);
                    $category->setIcon($icon);
                    
                    if ($category->update()) {
                        $success = true;
                        $message = 'Category updated successfully';
                    } else {
                        $errors[] = 'Failed to update category';
                    }
                }
            }
        }

        return [
            'category' => $category,
            'errors' => $errors,
            'success' => $success,
            'message' => $message ?? null
        ];
    }

    /**
     * Delete category
     */
    public function delete($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $category = Category::findById($id, $user->getId());
        if (!$category) {
            throw new \Exception('Category not found');
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid security token';
            } else {
                if ($category->delete()) {
                    $success = true;
                    $message = 'Category deleted successfully';
                } else {
                    $errors[] = 'Failed to delete category';
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
     * Export categories to CSV
     */
    public function export()
    {
        $exportController = new \StorageUnit\Controllers\ExportController();
        return $exportController->exportCategories();
    }

    /**
     * Export items in this category to CSV
     */
    public function exportItems($id)
    {
        $exportController = new \StorageUnit\Controllers\ExportController();
        return $exportController->exportItemsByCategory($id);
    }
}
