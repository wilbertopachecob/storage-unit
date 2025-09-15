<?php
/**
 * Category Model
 * Handles category management and database operations
 */

namespace StorageUnit\Models;

use StorageUnit\Core\Database;

class Category
{
    private $id;
    private $name;
    private $color;
    private $icon;
    private $userId;
    private $createdAt;
    private $updatedAt;

    public function __construct($name = null, $color = '#007bff', $icon = 'fas fa-box', $userId = null)
    {
        $this->name = $name;
        $this->color = $color ?? '#007bff';
        $this->icon = $icon ?? 'fas fa-box';
        $this->userId = $userId;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getColor() { return $this->color; }
    public function getIcon() { return $this->icon; }
    public function getUserId() { return $this->userId; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setColor($color) { $this->color = $color; }
    public function setIcon($icon) { $this->icon = $icon; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }

    /**
     * Create new category
     */
    public function create()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (!$this->validateCategoryData()) {
            throw new \Exception('Invalid category data');
        }

        $sql = "INSERT INTO categories (name, color, icon, user_id) VALUES (:name, :color, :icon, :user_id)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':icon', $this->icon);
        $stmt->bindParam(':user_id', $this->userId);

        if ($stmt->execute()) {
            $this->id = $conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Update category
     */
    public function update()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (!$this->validateCategoryData()) {
            throw new \Exception('Invalid category data');
        }

        $sql = "UPDATE categories SET name = :name, color = :color, icon = :icon WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':icon', $this->icon);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->userId);

        return $stmt->execute();
    }

    /**
     * Delete category
     */
    public function delete()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "DELETE FROM categories WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->userId);

        return $stmt->execute();
    }

    /**
     * Find category by ID
     */
    public static function findById($id, $userId = null)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM categories WHERE id = :id";
        $params = [':id' => $id];

        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $categoryData = $stmt->fetch();
        if ($categoryData) {
            $category = new self();
            $category->id = $categoryData['id'];
            $category->name = $categoryData['name'];
            $category->color = $categoryData['color'];
            $category->icon = $categoryData['icon'];
            $category->userId = $categoryData['user_id'];
            $category->createdAt = $categoryData['created_at'];
            $category->updatedAt = $categoryData['updated_at'];
            return $category;
        }

        return null;
    }

    /**
     * Get all categories for a user
     */
    public static function getAllForUser($userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM categories WHERE user_id = :user_id ORDER BY name ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get category with item count
     */
    public static function getWithItemCount($userId, $search = '', $sortBy = 'name', $sortOrder = 'asc', $limit = null, $offset = 0)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT c.*, COUNT(i.id) as item_count 
                FROM categories c 
                LEFT JOIN items i ON c.id = i.category_id 
                WHERE c.user_id = :user_id";
        
        $params = [':user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND c.name LIKE :search";
            $searchPattern = '%' . $search . '%';
            $params[':search'] = $searchPattern;
        }

        $sql .= " GROUP BY c.id";

        // Validate sort parameters
        $allowedSortFields = ['name', 'created_at', 'updated_at', 'item_count'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        
        $allowedSortOrders = ['asc', 'desc'];
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $sql .= " ORDER BY c.{$sortBy} {$sortOrder}";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Get count of categories for user
     */
    public static function getCountForUser($userId, $search = '')
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM categories WHERE user_id = :user_id";
        $params = [':user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND name LIKE :search";
            $searchPattern = '%' . $search . '%';
            $params[':search'] = $searchPattern;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }

    /**
     * Get items in a specific category
     */
    public static function getItemsInCategory($categoryId, $userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT i.*, c.name as category_name, c.color as category_color, c.icon as category_icon, 
                       l.name as location_name
                FROM items i 
                LEFT JOIN categories c ON i.category_id = c.id 
                LEFT JOIN locations l ON i.location_id = l.id 
                WHERE i.category_id = :category_id AND i.user_id = :user_id 
                ORDER BY i.updated_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get item count for a specific category
     */
    public static function getItemCount($categoryId, $userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM items WHERE category_id = :category_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Check if category name exists for user
     */
    public static function nameExists($name, $userId, $excludeId = null)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM categories WHERE name = :name AND user_id = :user_id";
        $params = [':name' => $name, ':user_id' => $userId];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Validate category data
     */
    private function validateCategoryData()
    {
        if (!$this->name || empty(trim($this->name))) {
            return false;
        }

        if (!$this->userId) {
            return false;
        }

        if (strlen($this->name) > 100) {
            return false;
        }

        return true;
    }

    /**
     * Convert to array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'icon' => $this->icon,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
