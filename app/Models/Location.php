<?php
/**
 * Location Model
 * Handles location management and database operations
 */

namespace StorageUnit\Models;

use StorageUnit\Core\Database;

class Location
{
    private $id;
    private $name;
    private $parentId;
    private $userId;
    private $createdAt;
    private $updatedAt;

    public function __construct($name = null, $parentId = null, $userId = null)
    {
        $this->name = $name;
        $this->parentId = $parentId;
        $this->userId = $userId;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getParentId() { return $this->parentId; }
    public function getUserId() { return $this->userId; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setName($name) { $this->name = $name; }
    public function setParentId($parentId) { $this->parentId = $parentId; }
    public function setUserId($userId) { $this->userId = $userId; }

    /**
     * Create new location
     */
    public function create()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (!$this->validateLocationData()) {
            throw new \Exception('Invalid location data');
        }

        $sql = "INSERT INTO locations (name, parent_id, user_id) VALUES (:name, :parent_id, :user_id)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':parent_id', $this->parentId);
        $stmt->bindParam(':user_id', $this->userId);

        if ($stmt->execute()) {
            $this->id = $conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Update location
     */
    public function update()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (!$this->validateLocationData()) {
            throw new \Exception('Invalid location data');
        }

        $sql = "UPDATE locations SET name = :name, parent_id = :parent_id WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':parent_id', $this->parentId);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->userId);

        return $stmt->execute();
    }

    /**
     * Delete location
     */
    public function delete()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "DELETE FROM locations WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->userId);

        return $stmt->execute();
    }

    /**
     * Find location by ID
     */
    public static function findById($id, $userId = null)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM locations WHERE id = :id";
        $params = [':id' => $id];

        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $locationData = $stmt->fetch();
        if ($locationData) {
            $location = new self();
            $location->id = $locationData['id'];
            $location->name = $locationData['name'];
            $location->parentId = $locationData['parent_id'];
            $location->userId = $locationData['user_id'];
            $location->createdAt = $locationData['created_at'];
            $location->updatedAt = $locationData['updated_at'];
            return $location;
        }

        return null;
    }

    /**
     * Get all locations for a user
     */
    public static function getAllForUser($userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM locations WHERE user_id = :user_id ORDER BY name ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get locations in hierarchical structure
     */
    public static function getHierarchy($userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM locations WHERE user_id = :user_id ORDER BY parent_id ASC, name ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        $locations = $stmt->fetchAll();
        return self::buildHierarchy($locations);
    }

    /**
     * Get location with item count
     */
    public static function getWithItemCount($userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT l.*, COUNT(i.id) as item_count 
                FROM locations l 
                LEFT JOIN items i ON l.id = i.location_id 
                WHERE l.user_id = :user_id 
                GROUP BY l.id 
                ORDER BY l.name ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get full location path
     */
    public function getFullPath()
    {
        $path = [$this->name];
        $current = $this;
        
        while ($current->parentId) {
            $parent = self::findById($current->parentId, $this->userId);
            if ($parent) {
                array_unshift($path, $parent->name);
                $current = $parent;
            } else {
                break;
            }
        }
        
        return implode(' → ', $path);
    }

    /**
     * Build hierarchical structure from flat array
     */
    private static function buildHierarchy($locations, $parentId = null)
    {
        $hierarchy = [];
        
        foreach ($locations as $location) {
            if ($location['parent_id'] == $parentId) {
                $children = self::buildHierarchy($locations, $location['id']);
                if (!empty($children)) {
                    $location['children'] = $children;
                }
                $hierarchy[] = $location;
            }
        }
        
        return $hierarchy;
    }

    /**
     * Check if location name exists for user
     */
    public static function nameExists($name, $userId, $parentId = null, $excludeId = null)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM locations WHERE name = :name AND user_id = :user_id AND parent_id ";
        $sql .= $parentId ? "= :parent_id" : "IS NULL";
        $params = [':name' => $name, ':user_id' => $userId];

        if ($parentId) {
            $params[':parent_id'] = $parentId;
        }

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Validate location data
     */
    private function validateLocationData()
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
            'parent_id' => $this->parentId,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
