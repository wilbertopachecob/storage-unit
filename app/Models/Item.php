<?php
/**
 * Item Model
 * Handles item management and database operations
 */

namespace StorageUnit\Models;

use StorageUnit\Core\Database;
use StorageUnit\Core\Security;

class Item
{
    private $id;
    private $title;
    private $description;
    private $qty;
    private $userId;
    private $categoryId;
    private $locationId;
    private $img;
    private $createdAt;
    private $updatedAt;
    private $db;

    public function __construct($title = null, $description = null, $qty = 1, $userId = null, $img = null, $categoryId = null, $locationId = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->qty = $qty;
        $this->userId = $userId;
        $this->categoryId = $categoryId;
        $this->locationId = $locationId;
        $this->img = $img;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getQty() { return $this->qty; }
    public function getUserId() { return $this->userId; }
    public function getCategoryId() { return $this->categoryId; }
    public function getLocationId() { return $this->locationId; }
    public function getImg() { return $this->img; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTitle($title) { $this->title = $title; }
    public function setDescription($description) { $this->description = $description; }
    public function setQty($qty) { $this->qty = $qty; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setCategoryId($categoryId) { $this->categoryId = $categoryId; }
    public function setLocationId($locationId) { $this->locationId = $locationId; }
    public function setImg($img) { $this->img = $img; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
    public function setDb($db) { $this->db = $db; }
    
    // Database getter
    public function getDb() { return $this->db; }

    /**
     * Create new item
     */
    public function create()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (!$this->validateItemData()) {
            throw new \Exception('Invalid item data');
        }

        $sql = "INSERT INTO items (title, description, qty, user_id, category_id, location_id, img) VALUES (:title, :description, :qty, :user_id, :category_id, :location_id, :img)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':qty', $this->qty);
        $stmt->bindParam(':user_id', $this->userId);
        $stmt->bindParam(':category_id', $this->categoryId);
        $stmt->bindParam(':location_id', $this->locationId);
        $stmt->bindParam(':img', $this->img);

        if ($stmt->execute()) {
            $this->id = $conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Update item
     */
    public function update()
    {
        if (!$this->id) {
            throw new \Exception('Item ID is required for update');
        }

        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (!$this->validateItemData()) {
            throw new \Exception('Invalid item data');
        }

        $sql = "UPDATE items SET title = :title, description = :description, qty = :qty, category_id = :category_id, location_id = :location_id, img = :img, updated_at = NOW() WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':qty', $this->qty);
        $stmt->bindParam(':category_id', $this->categoryId);
        $stmt->bindParam(':location_id', $this->locationId);
        $stmt->bindParam(':img', $this->img);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->userId);

        return $stmt->execute();
    }

    /**
     * Delete item
     */
    public function delete()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "DELETE FROM items WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->userId);

        return $stmt->execute();
    }

    /**
     * Find item by ID
     */
    public static function findById($id, $userId = null)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM items WHERE id = :id";
        $params = [':id' => $id];

        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $itemData = $stmt->fetch();
        if ($itemData) {
            $item = new self();
            $item->id = $itemData['id'];
            $item->title = $itemData['title'];
            $item->description = $itemData['description'];
            $item->qty = $itemData['qty'];
            $item->userId = $itemData['user_id'];
            $item->categoryId = $itemData['category_id'];
            $item->locationId = $itemData['location_id'];
            $item->img = $itemData['img'];
            $item->createdAt = $itemData['created_at'];
            $item->updatedAt = $itemData['updated_at'];
            return $item;
        }

        return null;
    }

    /**
     * Get all items for a user
     */
    public static function getAllForUser($userId, $limit = null, $offset = 0)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM items WHERE user_id = :user_id ORDER BY updated_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Search items by title
     */
    public static function searchByTitle($title, $userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM items WHERE user_id = :user_id AND title LIKE :title ORDER BY updated_at DESC";
        $stmt = $conn->prepare($sql);
        
        $searchTerm = '%' . $title . '%';
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':title', $searchTerm);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Enhanced search with multiple criteria
     */
    public static function search($searchTerm, $userId, $categoryId = null, $locationId = null, $limit = null, $offset = 0)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT i.*, c.name as category_name, c.color as category_color, l.name as location_name 
                FROM items i 
                LEFT JOIN categories c ON i.category_id = c.id 
                LEFT JOIN locations l ON i.location_id = l.id 
                WHERE i.user_id = :user_id";
        
        $params = [':user_id' => $userId];

        if (!empty($searchTerm)) {
            $sql .= " AND (i.title LIKE :search_term OR i.description LIKE :search_term OR c.name LIKE :search_term OR l.name LIKE :search_term)";
            $searchPattern = '%' . $searchTerm . '%';
            $params[':search_term'] = $searchPattern;
        }

        if ($categoryId) {
            $sql .= " AND i.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($locationId) {
            $sql .= " AND i.location_id = :location_id";
            $params[':location_id'] = $locationId;
        }

        $sql .= " ORDER BY i.updated_at DESC";

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
     * Get items with category and location data
     */
    public static function getAllWithDetails($userId, $search = '', $categoryId = null, $locationId = null, $sortBy = 'created_at', $sortOrder = 'desc', $limit = null, $offset = 0)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT i.*, c.name as category_name, c.color as category_color, c.icon as category_icon, 
                       l.name as location_name, l.parent_id as location_parent_id
                FROM items i 
                LEFT JOIN categories c ON i.category_id = c.id 
                LEFT JOIN locations l ON i.location_id = l.id 
                WHERE i.user_id = :user_id";
        
        $params = [':user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND (i.title LIKE :search OR i.description LIKE :search OR c.name LIKE :search OR l.name LIKE :search)";
            $searchPattern = '%' . $search . '%';
            $params[':search'] = $searchPattern;
        }

        if ($categoryId) {
            $sql .= " AND i.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($locationId) {
            $sql .= " AND i.location_id = :location_id";
            $params[':location_id'] = $locationId;
        }

        // Validate sort parameters
        $allowedSortFields = ['title', 'created_at', 'updated_at', 'qty'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        $allowedSortOrders = ['asc', 'desc'];
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $sql .= " ORDER BY i.{$sortBy} {$sortOrder}";
        
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
     * Get total quantity of items for a user
     */
    public static function getTotalQuantityForUser($userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT SUM(qty) FROM items WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * Get total count of items for a user
     */
    public static function getCountForUser($userId, $search = '', $categoryId = null, $locationId = null)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM items i 
                LEFT JOIN categories c ON i.category_id = c.id 
                LEFT JOIN locations l ON i.location_id = l.id 
                WHERE i.user_id = :user_id";
        
        $params = [':user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND (i.title LIKE :search OR i.description LIKE :search OR c.name LIKE :search OR l.name LIKE :search)";
            $searchPattern = '%' . $search . '%';
            $params[':search'] = $searchPattern;
        }

        if ($categoryId) {
            $sql .= " AND i.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($locationId) {
            $sql .= " AND i.location_id = :location_id";
            $params[':location_id'] = $locationId;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }

    /**
     * Get count of items with images
     */
    public static function getCountWithImages($userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM items WHERE user_id = :user_id AND img IS NOT NULL AND img != ''";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Get count of items without images
     */
    public static function getCountWithoutImages($userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM items WHERE user_id = :user_id AND (img IS NULL OR img = '')";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Get count of recent items (last N days)
     */
    public static function getRecentItemsCount($userId, $days = 7)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM items WHERE user_id = :user_id AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':days', $days, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Validate item data
     */
    private function validateItemData()
    {
        if (!$this->title || empty(trim($this->title))) {
            return false;
        }

        if (!$this->userId) {
            return false;
        }

        if ($this->qty < 1) {
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
            'title' => $this->title,
            'description' => $this->description,
            'qty' => $this->qty,
            'user_id' => $this->userId,
            'category_id' => $this->categoryId,
            'location_id' => $this->locationId,
            'img' => $this->img,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
