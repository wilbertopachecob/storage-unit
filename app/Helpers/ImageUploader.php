<?php
/**
 * Image Upload Helper
 * Handles profile picture uploads with validation and resizing
 */

namespace StorageUnit\Helpers;

class ImageUploader
{
    private $uploadDir;
    private $maxFileSize;
    private $allowedTypes;
    private $maxWidth;
    private $maxHeight;

    public function __construct($uploadDir = 'public/uploads/profiles/')
    {
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = 5 * 1024 * 1024; // 5MB
        $this->allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $this->maxWidth = 400;
        $this->maxHeight = 400;
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload and process profile picture
     */
    public function uploadProfilePicture($file, $userId)
    {
        // Validate file
        $validation = $this->validateFile($file);
        if (!$validation['valid']) {
            return $validation;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
        $filepath = $this->uploadDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'valid' => false,
                'message' => 'Failed to upload file'
            ];
        }

        // Resize image if needed
        $resizeResult = $this->resizeImage($filepath, $this->maxWidth, $this->maxHeight);
        if (!$resizeResult['success']) {
            // Delete the uploaded file if resize failed
            unlink($filepath);
            return [
                'valid' => false,
                'message' => 'Failed to process image: ' . $resizeResult['message']
            ];
        }

        return [
            'valid' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => '/' . str_replace('public/', '', $filepath)
        ];
    }

    /**
     * Validate uploaded file
     */
    private function validateFile($file)
    {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return [
                'valid' => false,
                'message' => 'No file uploaded'
            ];
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return [
                'valid' => false,
                'message' => 'File size too large. Maximum size is ' . ($this->maxFileSize / 1024 / 1024) . 'MB'
            ];
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedTypes)) {
            return [
                'valid' => false,
                'message' => 'Invalid file type. Allowed types: JPEG, PNG, GIF, WebP'
            ];
        }

        // Check if it's actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return [
                'valid' => false,
                'message' => 'File is not a valid image'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Resize image to fit within specified dimensions
     */
    private function resizeImage($filepath, $maxWidth, $maxHeight)
    {
        $imageInfo = getimagesize($filepath);
        if ($imageInfo === false) {
            return ['success' => false, 'message' => 'Invalid image file'];
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Calculate new dimensions
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = intval($originalWidth * $ratio);
        $newHeight = intval($originalHeight * $ratio);

        // If image is already smaller than max dimensions, no need to resize
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return ['success' => true];
        }

        // Create image resource based on type
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($filepath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($filepath);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($filepath);
                break;
            default:
                return ['success' => false, 'message' => 'Unsupported image type'];
        }

        if ($sourceImage === false) {
            return ['success' => false, 'message' => 'Failed to create image resource'];
        }

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize image
        if (!imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight)) {
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            return ['success' => false, 'message' => 'Failed to resize image'];
        }

        // Save resized image
        $success = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $success = imagejpeg($newImage, $filepath, 90);
                break;
            case 'image/png':
                $success = imagepng($newImage, $filepath, 8);
                break;
            case 'image/gif':
                $success = imagegif($newImage, $filepath);
                break;
            case 'image/webp':
                $success = imagewebp($newImage, $filepath, 90);
                break;
        }

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        if (!$success) {
            return ['success' => false, 'message' => 'Failed to save resized image'];
        }

        return ['success' => true];
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture($filename)
    {
        $filepath = $this->uploadDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return true; // File doesn't exist, consider it deleted
    }

    /**
     * Get default profile picture URL
     */
    public function getDefaultProfilePicture()
    {
        return '/img/default-avatar.png';
    }
}
