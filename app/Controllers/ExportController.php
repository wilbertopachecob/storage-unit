<?php
/**
 * Export Controller
 * Handles data export functionality
 */

namespace StorageUnit\Controllers;

use StorageUnit\Models\Item;
use StorageUnit\Models\Category;
use StorageUnit\Models\Location;
use StorageUnit\Models\User;
use StorageUnit\Core\Security;

class ExportController
{
    /**
     * Export items to CSV
     */
    public function exportItems()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        // Get items with full details
        $items = Item::getAllWithDetails($user->getId());
        $categories = Category::getAllForUser($user->getId());
        $locations = Location::getAllForUser($user->getId());

        // Create category and location lookup arrays
        $categoryLookup = [];
        foreach ($categories as $category) {
            $categoryLookup[$category['id']] = $category['name'];
        }

        $locationLookup = [];
        foreach ($locations as $location) {
            $locationLookup[$location['id']] = $this->getLocationPath($location, $locations);
        }

        // Set headers for CSV download
        $filename = 'storage_items_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write CSV headers
        $headers = [
            'ID',
            'Title',
            'Description',
            'Quantity',
            'Category',
            'Location',
            'Image',
            'Created At',
            'Updated At'
        ];
        fputcsv($output, $headers);

        // Write data rows
        foreach ($items as $item) {
            $row = [
                $item['id'],
                $item['title'],
                $item['description'],
                $item['qty'],
                $item['category_name'] ?? 'No Category',
                $item['location_name'] ?? 'No Location',
                $item['img'] ?? 'No Image',
                $item['created_at'],
                $item['updated_at']
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export items by category to CSV
     */
    public function exportItemsByCategory($categoryId)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        // Validate category belongs to user
        $category = Category::findById($categoryId, $user->getId());
        if (!$category) {
            throw new \Exception('Category not found');
        }

        // Get items filtered by category
        $items = Item::search('', $user->getId(), $categoryId);

        // Set headers for CSV download
        $filename = 'storage_items_category_' . $this->sanitizeFilename($category->getName()) . '_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write CSV headers
        $headers = [
            'ID',
            'Title',
            'Description',
            'Quantity',
            'Category',
            'Location',
            'Image',
            'Created At',
            'Updated At'
        ];
        fputcsv($output, $headers);

        // Write data rows
        foreach ($items as $item) {
            $row = [
                $item['id'],
                $item['title'],
                $item['description'],
                $item['qty'],
                $item['category_name'] ?? 'No Category',
                $item['location_name'] ?? 'No Location',
                $item['img'] ?? 'No Image',
                $item['created_at'],
                $item['updated_at']
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export items by location to CSV
     */
    public function exportItemsByLocation($locationId)
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        // Validate location belongs to user
        $location = Location::findById($locationId, $user->getId());
        if (!$location) {
            throw new \Exception('Location not found');
        }

        // Get items filtered by location
        $items = Item::search('', $user->getId(), null, $locationId);

        // Set headers for CSV download
        $filename = 'storage_items_location_' . $this->sanitizeFilename($location->getName()) . '_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write CSV headers
        $headers = [
            'ID',
            'Title',
            'Description',
            'Quantity',
            'Category',
            'Location',
            'Image',
            'Created At',
            'Updated At'
        ];
        fputcsv($output, $headers);

        // Write data rows
        foreach ($items as $item) {
            $row = [
                $item['id'],
                $item['title'],
                $item['description'],
                $item['qty'],
                $item['category_name'] ?? 'No Category',
                $item['location_name'] ?? 'No Location',
                $item['img'] ?? 'No Image',
                $item['created_at'],
                $item['updated_at']
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export search results to CSV
     */
    public function exportSearchResults()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $searchTerm = $_GET['q'] ?? '';
        $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $locationId = !empty($_GET['location_id']) ? (int)$_GET['location_id'] : null;

        // Get filtered items
        $items = Item::search($searchTerm, $user->getId(), $categoryId, $locationId);

        // Build filename based on filters
        $filename = 'storage_items_search';
        if ($searchTerm) {
            $filename .= '_' . $this->sanitizeFilename($searchTerm);
        }
        if ($categoryId) {
            $category = Category::findById($categoryId, $user->getId());
            if ($category) {
                $filename .= '_category_' . $this->sanitizeFilename($category->getName());
            }
        }
        if ($locationId) {
            $location = Location::findById($locationId, $user->getId());
            if ($location) {
                $filename .= '_location_' . $this->sanitizeFilename($location->getName());
            }
        }
        $filename .= '_' . date('Y-m-d_H-i-s') . '.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write CSV headers
        $headers = [
            'ID',
            'Title',
            'Description',
            'Quantity',
            'Category',
            'Location',
            'Image',
            'Created At',
            'Updated At'
        ];
        fputcsv($output, $headers);

        // Write data rows
        foreach ($items as $item) {
            $row = [
                $item['id'],
                $item['title'],
                $item['description'],
                $item['qty'],
                $item['category_name'] ?? 'No Category',
                $item['location_name'] ?? 'No Location',
                $item['img'] ?? 'No Image',
                $item['created_at'],
                $item['updated_at']
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export categories to CSV
     */
    public function exportCategories()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $categories = Category::getWithItemCount($user->getId());

        // Set headers for CSV download
        $filename = 'storage_categories_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write CSV headers
        $headers = [
            'ID',
            'Name',
            'Color',
            'Icon',
            'Item Count',
            'Created At',
            'Updated At'
        ];
        fputcsv($output, $headers);

        // Write data rows
        foreach ($categories as $category) {
            $row = [
                $category['id'],
                $category['name'],
                $category['color'],
                $category['icon'],
                $category['item_count'],
                $category['created_at'],
                $category['updated_at']
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export locations to CSV
     */
    public function exportLocations()
    {
        $user = User::getCurrentUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $locations = Location::getWithItemCount($user->getId());
        $hierarchy = Location::getHierarchy($user->getId());

        // Set headers for CSV download
        $filename = 'storage_locations_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write CSV headers
        $headers = [
            'ID',
            'Name',
            'Parent Location',
            'Full Path',
            'Item Count',
            'Created At',
            'Updated At'
        ];
        fputcsv($output, $headers);

        // Write data rows
        foreach ($locations as $location) {
            $parentName = '';
            if ($location['parent_id']) {
                $parent = array_filter($locations, function($loc) use ($location) {
                    return $loc['id'] == $location['parent_id'];
                });
                if (!empty($parent)) {
                    $parent = array_values($parent)[0];
                    $parentName = $parent['name'];
                }
            }

            $fullPath = $this->getLocationPath($location, $locations);

            $row = [
                $location['id'],
                $location['name'],
                $parentName,
                $fullPath,
                $location['item_count'],
                $location['created_at'],
                $location['updated_at']
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Get full location path
     */
    private function getLocationPath($location, $allLocations)
    {
        $path = [$location['name']];
        $current = $location;
        
        while ($current['parent_id']) {
            $parent = array_filter($allLocations, function($loc) use ($current) {
                return $loc['id'] == $current['parent_id'];
            });
            
            if (!empty($parent)) {
                $parent = array_values($parent)[0];
                array_unshift($path, $parent['name']);
                $current = $parent;
            } else {
                break;
            }
        }
        
        return implode(' → ', $path);
    }

    /**
     * Sanitize filename for safe download
     */
    private function sanitizeFilename($filename)
    {
        // Remove or replace invalid characters
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename); // Replace multiple underscores with single
        $filename = trim($filename, '_'); // Remove leading/trailing underscores
        return $filename ?: 'export';
    }
}
