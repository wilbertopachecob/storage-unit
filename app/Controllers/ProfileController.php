<?php
/**
 * Profile Controller
 * Handles user profile management and storage unit updates
 */

namespace StorageUnit\Controllers;

use StorageUnit\Models\User;
use StorageUnit\Core\Security;
use StorageUnit\Helpers\ImageUploader;

class ProfileController
{
    /**
     * Display profile page
     */
    public function index()
    {
        if (!User::isLoggedIn()) {
            header('Location: /signIn.php');
            exit;
        }

        $user = User::getCurrentUser();
        if (!$user) {
            header('Location: /signIn.php');
            exit;
        }

        include __DIR__ . '/../../resources/views/profile/index.php';
    }

    /**
     * Update storage unit information
     */
    public function updateStorageUnit()
    {
        if (!User::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $user = User::getCurrentUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        // Validate input
        $storageUnitName = trim($_POST['storage_unit_name'] ?? '');
        $storageUnitAddress = trim($_POST['storage_unit_address'] ?? '');
        $storageUnitLatitude = $_POST['storage_unit_latitude'] ?? null;
        $storageUnitLongitude = $_POST['storage_unit_longitude'] ?? null;

        // Validate required fields
        if (empty($storageUnitName)) {
            echo json_encode(['success' => false, 'message' => 'Storage unit name is required']);
            exit;
        }

        if (empty($storageUnitAddress)) {
            echo json_encode(['success' => false, 'message' => 'Storage unit address is required']);
            exit;
        }

        // Validate coordinates if provided
        if ($storageUnitLatitude !== null && (!is_numeric($storageUnitLatitude) || $storageUnitLatitude < -90 || $storageUnitLatitude > 90)) {
            echo json_encode(['success' => false, 'message' => 'Invalid latitude']);
            exit;
        }

        if ($storageUnitLongitude !== null && (!is_numeric($storageUnitLongitude) || $storageUnitLongitude < -180 || $storageUnitLongitude > 180)) {
            echo json_encode(['success' => false, 'message' => 'Invalid longitude']);
            exit;
        }

        try {
            // Update user storage unit information
            $user->setStorageUnitName($storageUnitName);
            $user->setStorageUnitAddress($storageUnitAddress);
            $user->setStorageUnitLatitude($storageUnitLatitude);
            $user->setStorageUnitLongitude($storageUnitLongitude);

            if ($user->updateStorageUnit()) {
                // Update session data
                $_SESSION['user_storage_unit_name'] = $storageUnitName;
                $_SESSION['user_storage_unit_address'] = $storageUnitAddress;
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Storage unit updated successfully',
                    'data' => [
                        'name' => $storageUnitName,
                        'address' => $storageUnitAddress,
                        'latitude' => $storageUnitLatitude,
                        'longitude' => $storageUnitLongitude
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update storage unit']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Get current user's storage unit information
     */
    public function getStorageUnit()
    {
        if (!User::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $user = User::getCurrentUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'name' => $user->getStorageUnitName(),
                'address' => $user->getStorageUnitAddress(),
                'latitude' => $user->getStorageUnitLatitude(),
                'longitude' => $user->getStorageUnitLongitude(),
                'updated_at' => $user->getStorageUnitUpdatedAt()
            ]
        ]);
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture()
    {
        if (!User::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $user = User::getCurrentUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        // Check if file was uploaded
        if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
            exit;
        }

        try {
            $uploader = new ImageUploader();
            $result = $uploader->uploadProfilePicture($_FILES['profile_picture'], $user->getId());

            if (!$result['valid']) {
                echo json_encode(['success' => false, 'message' => $result['message']]);
                exit;
            }

            // Delete old profile picture if exists
            if ($user->getProfilePicture()) {
                $uploader->deleteProfilePicture($user->getProfilePicture());
            }

            // Update user profile picture
            $user->setProfilePicture($result['filename']);
            if ($user->updateProfilePicture()) {
                // Update session data
                $_SESSION['user_profile_picture'] = $result['url'];
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile picture updated successfully',
                    'data' => [
                        'filename' => $result['filename'],
                        'url' => $result['url']
                    ]
                ]);
            } else {
                // Delete uploaded file if database update failed
                $uploader->deleteProfilePicture($result['filename']);
                echo json_encode(['success' => false, 'message' => 'Failed to update profile picture']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture()
    {
        if (!User::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $user = User::getCurrentUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        try {
            // Delete current profile picture file
            if ($user->getProfilePicture()) {
                $uploader = new ImageUploader();
                $uploader->deleteProfilePicture($user->getProfilePicture());
            }

            // Update user to remove profile picture
            $user->setProfilePicture(null);
            if ($user->updateProfilePicture()) {
                // Update session data
                unset($_SESSION['user_profile_picture']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile picture deleted successfully'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete profile picture']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
