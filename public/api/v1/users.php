<?php
/**
 * Users API v1
 * RESTful API for user management
 */

use StorageUnit\Controllers\ApiController;
use StorageUnit\Core\ApiResponse;
use StorageUnit\Models\User;

class UsersApiController extends ApiController
{
    /**
     * GET /api/v1/users/profile
     * Get current user profile
     */
    public function profile()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            
            // Return user profile without sensitive data
            $profile = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'storage_unit_name' => $user->getStorageUnitName(),
                'profile_picture' => $user->getProfilePicture(),
                'created_at' => $user->getCreatedAt(),
                'updated_at' => $user->getUpdatedAt()
            ];
            
            ApiResponse::success($profile);
        });
    }

    /**
     * PUT /api/v1/users/profile
     * Update current user profile
     */
    public function updateProfile()
    {
        $this->handleRequest('PUT', function() {
            $user = $this->getCurrentUser();
            $input = $this->getJsonInput();
            
            $errors = [];
            
            // Update username if provided
            if (isset($input['username'])) {
                $username = trim($input['username']);
                
                if (empty($username)) {
                    $errors['username'] = 'Username is required';
                } elseif (strlen($username) < 3) {
                    $errors['username'] = 'Username must be at least 3 characters';
                } elseif (strlen($username) > 50) {
                    $errors['username'] = 'Username must be less than 50 characters';
                } elseif (User::usernameExists($username, $user->getId())) {
                    $errors['username'] = 'Username already exists';
                } else {
                    $user->setUsername($username);
                }
            }
            
            // Update email if provided
            if (isset($input['email'])) {
                $email = trim($input['email']);
                
                if (empty($email)) {
                    $errors['email'] = 'Email is required';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Invalid email format';
                } elseif (User::emailExists($email, $user->getId())) {
                    $errors['email'] = 'Email already exists';
                } else {
                    $user->setEmail($email);
                }
            }
            
            // Update storage unit name if provided
            if (isset($input['storage_unit_name'])) {
                $storageUnitName = trim($input['storage_unit_name']);
                
                if (strlen($storageUnitName) > 100) {
                    $errors['storage_unit_name'] = 'Storage unit name must be less than 100 characters';
                } else {
                    $user->setStorageUnitName($storageUnitName);
                }
            }
            
            // Update profile picture if provided
            if (isset($input['profile_picture'])) {
                $user->setProfilePicture($input['profile_picture']);
            }
            
            if (!empty($errors)) {
                ApiResponse::validationError($errors, 'Validation failed');
            }
            
            if ($user->update()) {
                // Return updated profile
                $profile = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'storage_unit_name' => $user->getStorageUnitName(),
                    'profile_picture' => $user->getProfilePicture(),
                    'created_at' => $user->getCreatedAt(),
                    'updated_at' => $user->getUpdatedAt()
                ];
                
                ApiResponse::success($profile, 'Profile updated successfully');
            } else {
                ApiResponse::error('Failed to update profile', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * PATCH /api/v1/users/profile
     * Partially update current user profile
     */
    public function patchProfile()
    {
        $this->handleRequest('PATCH', function() {
            $user = $this->getCurrentUser();
            $input = $this->getJsonInput();
            
            $errors = [];
            
            // Update only provided fields
            if (isset($input['username'])) {
                $username = trim($input['username']);
                
                if (empty($username)) {
                    $errors['username'] = 'Username is required';
                } elseif (strlen($username) < 3) {
                    $errors['username'] = 'Username must be at least 3 characters';
                } elseif (strlen($username) > 50) {
                    $errors['username'] = 'Username must be less than 50 characters';
                } elseif (User::usernameExists($username, $user->getId())) {
                    $errors['username'] = 'Username already exists';
                } else {
                    $user->setUsername($username);
                }
            }
            
            if (isset($input['email'])) {
                $email = trim($input['email']);
                
                if (empty($email)) {
                    $errors['email'] = 'Email is required';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Invalid email format';
                } elseif (User::emailExists($email, $user->getId())) {
                    $errors['email'] = 'Email already exists';
                } else {
                    $user->setEmail($email);
                }
            }
            
            if (isset($input['storage_unit_name'])) {
                $storageUnitName = trim($input['storage_unit_name']);
                
                if (strlen($storageUnitName) > 100) {
                    $errors['storage_unit_name'] = 'Storage unit name must be less than 100 characters';
                } else {
                    $user->setStorageUnitName($storageUnitName);
                }
            }
            
            if (isset($input['profile_picture'])) {
                $user->setProfilePicture($input['profile_picture']);
            }
            
            if (!empty($errors)) {
                ApiResponse::validationError($errors, 'Validation failed');
            }
            
            if ($user->update()) {
                // Return updated profile
                $profile = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'storage_unit_name' => $user->getStorageUnitName(),
                    'profile_picture' => $user->getProfilePicture(),
                    'created_at' => $user->getCreatedAt(),
                    'updated_at' => $user->getUpdatedAt()
                ];
                
                ApiResponse::success($profile, 'Profile updated successfully');
            } else {
                ApiResponse::error('Failed to update profile', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * POST /api/v1/users/change-password
     * Change user password
     */
    public function changePassword()
    {
        $this->handleRequest('POST', function() {
            $user = $this->getCurrentUser();
            $input = $this->getJsonInput();
            
            // Validate required fields
            $this->validateRequiredFields($input, ['current_password', 'new_password', 'confirm_password']);
            
            $currentPassword = $input['current_password'];
            $newPassword = $input['new_password'];
            $confirmPassword = $input['confirm_password'];
            
            $errors = [];
            
            // Verify current password
            if (!password_verify($currentPassword, $user->getPassword())) {
                $errors['current_password'] = 'Current password is incorrect';
            }
            
            // Validate new password
            if (strlen($newPassword) < 8) {
                $errors['new_password'] = 'New password must be at least 8 characters';
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = 'Password confirmation does not match';
            }
            
            if (!empty($errors)) {
                ApiResponse::validationError($errors, 'Validation failed');
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $user->setPassword($hashedPassword);
            
            if ($user->update()) {
                ApiResponse::success(null, 'Password changed successfully');
            } else {
                ApiResponse::error('Failed to change password', 500, 'Internal Server Error');
            }
        });
    }

    /**
     * GET /api/v1/users/stats
     * Get user statistics
     */
    public function stats()
    {
        $this->handleRequest('GET', function() {
            $user = $this->getCurrentUser();
            
            try {
                // Get user statistics
                $stats = [
                    'total_items' => \StorageUnit\Models\Item::getCountForUser($user->getId()),
                    'total_quantity' => \StorageUnit\Models\Item::getTotalQuantityForUser($user->getId()),
                    'total_categories' => \StorageUnit\Models\Category::getCountForUser($user->getId()),
                    'total_locations' => \StorageUnit\Models\Location::getCountForUser($user->getId()),
                    'items_with_images' => \StorageUnit\Models\Item::getCountWithImages($user->getId()),
                    'items_without_images' => \StorageUnit\Models\Item::getCountWithoutImages($user->getId()),
                    'recent_items_count' => \StorageUnit\Models\Item::getRecentItemsCount($user->getId(), 7), // Last 7 days
                    'account_created' => $user->getCreatedAt()
                ];
                
                ApiResponse::success($stats);
                
            } catch (Exception $e) {
                ApiResponse::error('Failed to retrieve user statistics', 500, 'Internal Server Error');
            }
        });
    }
}

// Route the request
$controller = new UsersApiController();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Determine which method to call based on path
if (count($pathParts) >= 4 && $pathParts[3] === 'profile') {
    if ($method === 'GET') {
        $controller->profile();
    } elseif ($method === 'PUT') {
        $controller->updateProfile();
    } elseif ($method === 'PATCH') {
        $controller->patchProfile();
    } else {
        ApiResponse::methodNotAllowed();
    }
} elseif (count($pathParts) >= 4 && $pathParts[3] === 'change-password') {
    $controller->changePassword();
} elseif (count($pathParts) >= 4 && $pathParts[3] === 'stats') {
    $controller->stats();
} else {
    ApiResponse::methodNotAllowed();
}
?>
