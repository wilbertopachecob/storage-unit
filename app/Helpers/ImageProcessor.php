<?php
/**
 * Image Processor Helper
 * Handles image optimization, resizing, and compression
 */

namespace StorageUnit\Helpers;

class ImageProcessor
{
    private $maxWidth = 800;
    private $maxHeight = 600;
    private $quality = 85;
    private $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

    public function __construct($maxWidth = 800, $maxHeight = 600, $quality = 85)
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->quality = $quality;
    }

    /**
     * Process and optimize uploaded image
     */
    public function processImage($sourcePath, $destinationPath, $options = [])
    {
        if (!file_exists($sourcePath)) {
            throw new \Exception('Source image file not found');
        }

        // Get image info
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new \Exception('Invalid image file');
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Check if image needs processing
        if ($originalWidth <= $this->maxWidth && $originalHeight <= $this->maxHeight) {
            // Just copy the file if it's already small enough
            return copy($sourcePath, $destinationPath);
        }

        // Calculate new dimensions
        $dimensions = $this->calculateDimensions($originalWidth, $originalHeight);
        $newWidth = $dimensions['width'];
        $newHeight = $dimensions['height'];

        // Create image resource based on type
        $sourceImage = $this->createImageResource($sourcePath, $mimeType);
        if (!$sourceImage) {
            throw new \Exception('Failed to create image resource');
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
        imagecopyresampled(
            $newImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );

        // Save optimized image
        $result = $this->saveImage($newImage, $destinationPath, $mimeType, $options);

        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        return $result;
    }

    /**
     * Create thumbnail
     */
    public function createThumbnail($sourcePath, $destinationPath, $thumbWidth = 200, $thumbHeight = 200)
    {
        if (!file_exists($sourcePath)) {
            throw new \Exception('Source image file not found');
        }

        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new \Exception('Invalid image file');
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Calculate thumbnail dimensions (crop to square)
        $dimensions = $this->calculateThumbnailDimensions($originalWidth, $originalHeight, $thumbWidth, $thumbHeight);
        
        $sourceImage = $this->createImageResource($sourcePath, $mimeType);
        if (!$sourceImage) {
            throw new \Exception('Failed to create image resource');
        }

        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Preserve transparency
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $thumbWidth, $thumbHeight, $transparent);
        }

        // Create thumbnail with crop
        imagecopyresampled(
            $thumbnail, $sourceImage,
            0, 0, $dimensions['cropX'], $dimensions['cropY'],
            $thumbWidth, $thumbHeight,
            $dimensions['cropWidth'], $dimensions['cropHeight']
        );

        $result = $this->saveImage($thumbnail, $destinationPath, $mimeType, ['quality' => 90]);

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return $result;
    }

    /**
     * Calculate new dimensions maintaining aspect ratio
     */
    private function calculateDimensions($originalWidth, $originalHeight)
    {
        $ratio = $originalWidth / $originalHeight;

        if ($originalWidth > $originalHeight) {
            // Landscape
            $newWidth = min($this->maxWidth, $originalWidth);
            $newHeight = $newWidth / $ratio;
            
            if ($newHeight > $this->maxHeight) {
                $newHeight = $this->maxHeight;
                $newWidth = $newHeight * $ratio;
            }
        } else {
            // Portrait or square
            $newHeight = min($this->maxHeight, $originalHeight);
            $newWidth = $newHeight * $ratio;
            
            if ($newWidth > $this->maxWidth) {
                $newWidth = $this->maxWidth;
                $newHeight = $newWidth / $ratio;
            }
        }

        return [
            'width' => (int) $newWidth,
            'height' => (int) $newHeight
        ];
    }

    /**
     * Calculate thumbnail dimensions with crop
     */
    private function calculateThumbnailDimensions($originalWidth, $originalHeight, $thumbWidth, $thumbHeight)
    {
        $ratio = $originalWidth / $originalHeight;
        $thumbRatio = $thumbWidth / $thumbHeight;

        if ($ratio > $thumbRatio) {
            // Image is wider than thumbnail ratio
            $cropHeight = $originalHeight;
            $cropWidth = $originalHeight * $thumbRatio;
            $cropX = ($originalWidth - $cropWidth) / 2;
            $cropY = 0;
        } else {
            // Image is taller than thumbnail ratio
            $cropWidth = $originalWidth;
            $cropHeight = $originalWidth / $thumbRatio;
            $cropX = 0;
            $cropY = ($originalHeight - $cropHeight) / 2;
        }

        return [
            'cropX' => (int) $cropX,
            'cropY' => (int) $cropY,
            'cropWidth' => (int) $cropWidth,
            'cropHeight' => (int) $cropHeight
        ];
    }

    /**
     * Create image resource from file
     */
    private function createImageResource($path, $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    /**
     * Save image to file
     */
    private function saveImage($imageResource, $path, $mimeType, $options = [])
    {
        $quality = $options['quality'] ?? $this->quality;

        switch ($mimeType) {
            case 'image/jpeg':
                return imagejpeg($imageResource, $path, $quality);
            case 'image/png':
                // PNG quality is 0-9, we need to convert from 0-100
                $pngQuality = 9 - round(($quality / 100) * 9);
                return imagepng($imageResource, $path, $pngQuality);
            case 'image/gif':
                return imagegif($imageResource, $path);
            case 'image/webp':
                return imagewebp($imageResource, $path, $quality);
            default:
                return false;
        }
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSize($path)
    {
        if (!file_exists($path)) {
            return '0 B';
        }

        $bytes = filesize($path);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Validate image file
     */
    public function validateImage($file)
    {
        $errors = [];

        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'No file uploaded';
            return $errors;
        }

        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            $errors[] = 'File size too large (max 10MB)';
        }

        // Check file type
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            $errors[] = 'Invalid image file';
            return $errors;
        }

        $mimeType = $imageInfo['mime'];
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if (!in_array($mimeType, $allowedMimes)) {
            $errors[] = 'Invalid file type. Allowed: JPEG, PNG, GIF, WebP';
        }

        // Check dimensions (max 5000x5000)
        if ($imageInfo[0] > 5000 || $imageInfo[1] > 5000) {
            $errors[] = 'Image dimensions too large (max 5000x5000)';
        }

        return $errors;
    }

    /**
     * Generate optimized filename
     */
    public function generateOptimizedFilename($originalName, $suffix = '')
    {
        $pathInfo = pathinfo($originalName);
        $extension = strtolower($pathInfo['extension']);
        $name = $pathInfo['filename'];
        
        // Sanitize filename
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        $name = trim($name, '_');
        
        if (empty($name)) {
            $name = 'image';
        }
        
        $suffix = $suffix ? '_' . $suffix : '';
        return $name . $suffix . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Clean up old files
     */
    public function cleanupOldFiles($directory, $maxAge = 86400) // 24 hours
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = glob($directory . '/*');
        $cleaned = 0;

        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > $maxAge) {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }

        return $cleaned;
    }
}
