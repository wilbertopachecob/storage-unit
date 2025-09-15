<?php
/**
 * Authentication API v1
 * RESTful API for authentication
 */

use StorageUnit\Core\ApiResponse;
use StorageUnit\Models\User;
use StorageUnit\Core\Security;

class AuthApiController
{
    /**
     * POST /api/v1/auth/login
     * Authenticate user and create session
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::methodNotAllowed();
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ApiResponse::error('Invalid JSON input', 400, 'Bad Request');
        }
        
        // Validate required fields
        if (empty($input['username']) || empty($input['password'])) {
            ApiResponse::validationError([
                'username' => 'Username is required',
                'password' => 'Password is required'
            ], 'Validation failed');
        }
        
        $username = trim($input['username']);
        $password = $input['password'];
        
        try {
            // Find user by username or email
            $user = User::findByUsernameOrEmail($username);
            
            if (!$user || !password_verify($password, $user->getPassword())) {
                ApiResponse::error('Invalid credentials', 401, 'Unauthorized');
            }
            
            // Create session
            session_start();
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['authenticated'] = true;
            
            // Return user data (without password)
            $userData = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'storage_unit_name' => $user->getStorageUnitName(),
                'profile_picture' => $user->getProfilePicture(),
                'created_at' => $user->getCreatedAt()
            ];
            
            ApiResponse::success($userData, 'Login successful');
            
        } catch (Exception $e) {
            ApiResponse::error('Authentication failed', 500, 'Internal Server Error');
        }
    }

    /**
     * POST /api/v1/auth/register
     * Register a new user
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::methodNotAllowed();
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ApiResponse::error('Invalid JSON input', 400, 'Bad Request');
        }
        
        // Validate required fields
        $requiredFields = ['username', 'email', 'password', 'confirm_password'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        if (!empty($errors)) {
            ApiResponse::validationError($errors, 'Validation failed');
        }
        
        $username = trim($input['username']);
        $email = trim($input['email']);
        $password = $input['password'];
        $confirmPassword = $input['confirm_password'];
        
        // Validate username
        if (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        } elseif (strlen($username) > 50) {
            $errors['username'] = 'Username must be less than 50 characters';
        } elseif (User::usernameExists($username)) {
            $errors['username'] = 'Username already exists';
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif (User::emailExists($email)) {
            $errors['email'] = 'Email already exists';
        }
        
        // Validate password
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Password confirmation does not match';
        }
        
        if (!empty($errors)) {
            ApiResponse::validationError($errors, 'Validation failed');
        }
        
        try {
            // Create new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user = new User($username, $email, $hashedPassword);
            
            if ($user->create()) {
                // Create session
                session_start();
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['authenticated'] = true;
                
                // Return user data (without password)
                $userData = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'storage_unit_name' => $user->getStorageUnitName(),
                    'profile_picture' => $user->getProfilePicture(),
                    'created_at' => $user->getCreatedAt()
                ];
                
                ApiResponse::created($userData, 'User registered successfully');
            } else {
                ApiResponse::error('Failed to create user', 500, 'Internal Server Error');
            }
            
        } catch (Exception $e) {
            ApiResponse::error('Registration failed', 500, 'Internal Server Error');
        }
    }

    /**
     * POST /api/v1/auth/logout
     * Logout user and destroy session
     */
    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::methodNotAllowed();
        }
        
        session_start();
        
        // Destroy session
        session_destroy();
        
        ApiResponse::success(null, 'Logout successful');
    }

    /**
     * GET /api/v1/auth/me
     * Get current authenticated user
     */
    public function me()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            ApiResponse::methodNotAllowed();
        }
        
        session_start();
        
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
            ApiResponse::unauthorized('User not authenticated');
        }
        
        try {
            $user = User::getCurrentUser();
            
            if (!$user) {
                ApiResponse::unauthorized('User not found');
            }
            
            // Return user data (without password)
            $userData = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'storage_unit_name' => $user->getStorageUnitName(),
                'profile_picture' => $user->getProfilePicture(),
                'created_at' => $user->getCreatedAt(),
                'updated_at' => $user->getUpdatedAt()
            ];
            
            ApiResponse::success($userData);
            
        } catch (Exception $e) {
            ApiResponse::error('Failed to retrieve user data', 500, 'Internal Server Error');
        }
    }

    /**
     * POST /api/v1/auth/refresh
     * Refresh authentication token/session
     */
    public function refresh()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::methodNotAllowed();
        }
        
        session_start();
        
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
            ApiResponse::unauthorized('User not authenticated');
        }
        
        try {
            $user = User::getCurrentUser();
            
            if (!$user) {
                ApiResponse::unauthorized('User not found');
            }
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            // Return user data
            $userData = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'storage_unit_name' => $user->getStorageUnitName(),
                'profile_picture' => $user->getProfilePicture(),
                'created_at' => $user->getCreatedAt(),
                'updated_at' => $user->getUpdatedAt()
            ];
            
            ApiResponse::success($userData, 'Session refreshed successfully');
            
        } catch (Exception $e) {
            ApiResponse::error('Failed to refresh session', 500, 'Internal Server Error');
        }
    }
}

// Route the request
$controller = new AuthApiController();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Determine which method to call based on path
if (count($pathParts) >= 4) {
    $action = $pathParts[3];
    
    switch ($action) {
        case 'login':
            $controller->login();
            break;
        case 'register':
            $controller->register();
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'me':
            $controller->me();
            break;
        case 'refresh':
            $controller->refresh();
            break;
        default:
            ApiResponse::notFound('Authentication endpoint not found');
            break;
    }
} else {
    ApiResponse::notFound('Authentication endpoint not found');
}
?>
