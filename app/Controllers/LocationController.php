<?php
/**
 * Location Controller
 * Handles location management operations
 */

namespace StorageUnit\Controllers;

use StorageUnit\Models\Location;
use StorageUnit\Models\User;
use StorageUnit\Core\Security;

class LocationController
{
    /**
     * Get all locations for current user
     */
    public function index()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $locations = Location::getWithItemCount($user->getId());
        $hierarchy = Location::getHierarchy($user->getId());
        
        return [
            'locations' => $locations,
            'hierarchy' => $hierarchy,
            'total_count' => count($locations)
        ];
    }

    /**
     * Show specific location
     */
    public function show($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $location = Location::findById($id, $user->getId());
        if (!$location) {
            throw new \Exception('Location not found');
        }

        return ['location' => $location];
    }

    /**
     * Create new location
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
                $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

                // Validate input
                if (empty($name)) {
                    $errors[] = 'Location name is required';
                }

                if (strlen($name) > 100) {
                    $errors[] = 'Location name must be less than 100 characters';
                }

                // Validate parent location exists and belongs to user
                if ($parentId) {
                    $parentLocation = Location::findById($parentId, $user->getId());
                    if (!$parentLocation) {
                        $errors[] = 'Parent location not found';
                    }
                }

                // Check if name already exists in the same parent
                if (Location::nameExists($name, $user->getId(), $parentId)) {
                    $errors[] = 'Location name already exists in this parent location';
                }

                if (empty($errors)) {
                    $location = new Location($name, $parentId, $user->getId());
                    
                    if ($location->create()) {
                        $success = true;
                        $message = 'Location created successfully';
                    } else {
                        $errors[] = 'Failed to create location';
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
     * Update location
     */
    public function update($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $location = Location::findById($id, $user->getId());
        if (!$location) {
            throw new \Exception('Location not found');
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
                $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

                // Validate input
                if (empty($name)) {
                    $errors[] = 'Location name is required';
                }

                if (strlen($name) > 100) {
                    $errors[] = 'Location name must be less than 100 characters';
                }

                // Prevent circular reference
                if ($parentId == $id) {
                    $errors[] = 'Location cannot be its own parent';
                }

                // Validate parent location exists and belongs to user
                if ($parentId) {
                    $parentLocation = Location::findById($parentId, $user->getId());
                    if (!$parentLocation) {
                        $errors[] = 'Parent location not found';
                    }
                }

                // Check if name already exists in the same parent (excluding current location)
                if (Location::nameExists($name, $user->getId(), $parentId, $id)) {
                    $errors[] = 'Location name already exists in this parent location';
                }

                if (empty($errors)) {
                    $location->setName($name);
                    $location->setParentId($parentId);
                    
                    if ($location->update()) {
                        $success = true;
                        $message = 'Location updated successfully';
                    } else {
                        $errors[] = 'Failed to update location';
                    }
                }
            }
        }

        return [
            'location' => $location,
            'errors' => $errors,
            'success' => $success,
            'message' => $message ?? null
        ];
    }

    /**
     * Delete location
     */
    public function delete($id)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $location = Location::findById($id, $user->getId());
        if (!$location) {
            throw new \Exception('Location not found');
        }

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid security token';
            } else {
                if ($location->delete()) {
                    $success = true;
                    $message = 'Location deleted successfully';
                } else {
                    $errors[] = 'Failed to delete location';
                }
            }
        }

        return [
            'errors' => $errors,
            'success' => $success,
            'message' => $message ?? null
        ];
    }
}
