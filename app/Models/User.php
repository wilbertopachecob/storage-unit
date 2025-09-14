<?php
/**
 * User Model
 * Handles user authentication and management
 */

namespace StorageUnit\Models;

use StorageUnit\Core\Database;
use StorageUnit\Core\Security;

class User
{
    private $id;
    private $email;
    private $name;
    private $password;
    private $createdAt;
    private $updatedAt;

    public function __construct($email = null, $password = null, $name = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getName() { return $this->name; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setEmail($email) { $this->email = $email; }
    public function setName($name) { $this->name = $name; }
    public function setPassword($password) { $this->password = $password; }

    /**
     * Create new user
     */
    public function create()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Validate input
        if (!$this->validateUserData()) {
            throw new \Exception('Invalid user data');
        }

        // Check if email already exists
        if ($this->emailExists($this->email)) {
            throw new \Exception('Email already exists');
        }

        $sql = "INSERT INTO users (email, password, name) VALUES (:email, :password, :name)";
        $stmt = $conn->prepare($sql);
        
        $hashedPassword = Security::hashPassword($this->password);
        
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':name', $this->name);

        if ($stmt->execute()) {
            $this->id = $conn->lastInsertId();
            $this->setSessionData();
            return true;
        }

        return false;
    }

    /**
     * Add user (alias for create)
     */
    public function addUser()
    {
        return $this->create();
    }

    /**
     * Authenticate user
     */
    public function authenticate()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user && Security::verifyPassword($this->password, $user['password'])) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->createdAt = $user['created_at'];
            $this->updatedAt = $user['updated_at'];
            $this->setSessionData();
            return true;
        }

        return false;
    }

    /**
     * Login user (alias for authenticate)
     */
    public function login()
    {
        return $this->authenticate();
    }

    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Find user by ID
     */
    public static function findById($id)
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $userData = $stmt->fetch();
        if ($userData) {
            $user = new self();
            $user->id = $userData['id'];
            $user->email = $userData['email'];
            $user->name = $userData['name'];
            $user->createdAt = $userData['created_at'];
            $user->updatedAt = $userData['updated_at'];
            return $user;
        }

        return null;
    }

    /**
     * Update user
     */
    public function update()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "UPDATE users SET name = :name, email = :email";
        $params = [':name' => $this->name, ':email' => $this->email];

        if ($this->password) {
            $sql .= ", password = :password";
            $params[':password'] = Security::hashPassword($this->password);
        }

        $sql .= " WHERE id = :id";
        $params[':id'] = $this->id;

        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete user
     */
    public function delete()
    {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Set session data after login/registration
     */
    private function setSessionData()
    {
        $_SESSION['user_id'] = $this->id;
        $_SESSION['user_name'] = $this->name;
        $_SESSION['user_email'] = $this->email;
    }

    /**
     * Validate user data
     */
    private function validateUserData()
    {
        if (!$this->email || !Security::validateEmail($this->email)) {
            return false;
        }

        if (!$this->password || !Security::validatePassword($this->password)) {
            return false;
        }

        if (!$this->name || empty(trim($this->name))) {
            return false;
        }

        return true;
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        // Clear all session data
        $_SESSION = array();
        
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Start a new session
        session_start();
        
        // Regenerate session ID for security
        session_regenerate_id(true);
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get current user from session
     */
    public static function getCurrentUser()
    {
        if (self::isLoggedIn()) {
            return self::findById($_SESSION['user_id']);
        }
        return null;
    }
}
