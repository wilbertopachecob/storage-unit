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
    private $description;
    private $address;
    private $latitude;
    private $longitude;
    private $parentId;
    private $userId;
    private $createdAt;
    private $updatedAt;

    public function __construct($name = null, $description = null, $address = null, $latitude = null, $longitude = null, $userId = null, $parentId = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->address = $address;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->userId = $userId;
        $this->parentId = $parentId;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getAddress() { return $this->address; }
    public function getLatitude() { return $this->latitude; }
    public function getLongitude() { return $this->longitude; }
    public function getParentId() { return $this->parentId; }
    public function getUserId() { return $this->userId; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; }
    public function setAddress($address) { $this->address = $address; }
    public function setLatitude($latitude) { $this->latitude = $latitude; }
    public function setLongitude($longitude) { $this->longitude = $longitude; }
    public function setParentId($parentId) { $this->parentId = $parentId; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }

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

        $sql = "INSERT INTO locations (name, description, address, latitude, longitude, parent_id, user_id) VALUES (:name, :description, :address, :latitude, :longitude, :parent_id, :user_id)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':latitude', $this->latitude);
        $stmt->bindParam(':longitude', $this->longitude);
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

        $sql = "UPDATE locations SET name = :name, description = :description, address = :address, latitude = :latitude, longitude = :longitude, parent_id = :parent_id WHERE id = :id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':latitude', $this->latitude);
        $stmt->bindParam(':longitude', $this->longitude);
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
            $location->description = $locationData['description'];
            $location->address = $locationData['address'];
            $location->latitude = $locationData['latitude'];
            $location->longitude = $locationData['longitude'];
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
    public static function getWithItemCount($userId, $search = '', $sortBy = 'name', $sortOrder = 'asc', $limit = null, $offset = 0)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT l.*, COUNT(i.id) as item_count 
                FROM locations l 
                LEFT JOIN items i ON l.id = i.location_id 
                WHERE l.user_id = :user_id";
        
        $params = [':user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND l.name LIKE :search";
            $searchPattern = '%' . $search . '%';
            $params[':search'] = $searchPattern;
        }

        $sql .= " GROUP BY l.id";

        // Validate sort parameters
        $allowedSortFields = ['name', 'created_at', 'updated_at', 'item_count'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        
        $allowedSortOrders = ['asc', 'desc'];
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $sql .= " ORDER BY l.{$sortBy} {$sortOrder}";

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
     * Get count of locations for user
     */
    public static function getCountForUser($userId, $search = '')
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM locations WHERE user_id = :user_id";
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
     * Get items in a specific location
     */
    public static function getItemsInLocation($locationId, $userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT i.*, c.name as category_name, c.color as category_color, c.icon as category_icon, 
                       l.name as location_name
                FROM items i 
                LEFT JOIN categories c ON i.category_id = c.id 
                LEFT JOIN locations l ON i.location_id = l.id 
                WHERE i.location_id = :location_id AND i.user_id = :user_id 
                ORDER BY i.updated_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':location_id', $locationId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get item count for a specific location
     */
    public static function getItemCount($locationId, $userId)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM items WHERE location_id = :location_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':location_id', $locationId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchColumn();
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
            'description' => $this->description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'parent_id' => $this->parentId,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
