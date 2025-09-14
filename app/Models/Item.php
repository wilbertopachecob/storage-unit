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
    private $img;
    private $createdAt;
    private $updatedAt;
    private $db;

    public function __construct($title = null, $description = null, $qty = 1, $userId = null, $img = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->qty = $qty;
        $this->userId = $userId;
        $this->img = $img;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getQty() { return $this->qty; }
    public function getUserId() { return $this->userId; }
    public function getImg() { return $this->img; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setTitle($title) { $this->title = $title; }
    public function setDescription($description) { $this->description = $description; }
    public function setQty($qty) { $this->qty = $qty; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setImg($img) { $this->img = $img; }
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

        $sql = "INSERT INTO items (title, description, qty, user_id, img) VALUES (:title, :description, :qty, :user_id, :img)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':qty', $this->qty);
        $stmt->bindParam(':user_id', $this->userId);
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
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (!$this->validateItemData()) {
            throw new \Exception('Invalid item data');
        }

        $sql = "UPDATE items SET title = :title, description = :description, qty = :qty, img = :img WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':qty', $this->qty);
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
    public static function getCountForUser($userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM items WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
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
            'img' => $this->img,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
