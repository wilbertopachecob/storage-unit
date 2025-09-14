<?php
/**
 * Image Optimization Script
 * Optimizes existing images in the uploads directory
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';

use StorageUnit\Helpers\ImageProcessor;

class ImageOptimizer
{
    private $imageProcessor;
    private $uploadsDir;
    private $thumbnailsDir;
    private $stats = [
        'processed' => 0,
        'skipped' => 0,
        'errors' => 0,
        'saved_space' => 0
    ];

    public function __construct()
    {
        $this->imageProcessor = new ImageProcessor();
        $this->uploadsDir = __DIR__ . '/../public/uploads/';
        $this->thumbnailsDir = __DIR__ . '/../public/uploads/thumbnails/';
        
        // Create thumbnails directory if it doesn't exist
        if (!is_dir($this->thumbnailsDir)) {
            mkdir($this->thumbnailsDir, 0755, true);
        }
    }

    /**
     * Optimize all images in uploads directory
     */
    public function optimizeAll()
    {
        echo "🖼️  Starting image optimization...\n";
        echo "📁 Uploads directory: {$this->uploadsDir}\n";
        echo "📁 Thumbnails directory: {$this->thumbnailsDir}\n\n";

        $files = glob($this->uploadsDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        
        if (empty($files)) {
            echo "❌ No images found to optimize.\n";
            return;
        }

        echo "📊 Found " . count($files) . " images to process...\n\n";

        foreach ($files as $file) {
            $this->optimizeImage($file);
        }

        $this->displayStats();
    }

    /**
     * Optimize a single image
     */
    private function optimizeImage($filePath)
    {
        $filename = basename($filePath);
        $originalSize = filesize($filePath);
        
        echo "Processing: {$filename} (" . $this->formatBytes($originalSize) . ")... ";

        try {
            // Check if image needs optimization
            $imageInfo = getimagesize($filePath);
            if (!$imageInfo) {
                echo "❌ Invalid image\n";
                $this->stats['errors']++;
                return;
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            // Skip if already small enough
            if ($width <= 800 && $height <= 600) {
                echo "⏭️  Skipped (already optimized)\n";
                $this->stats['skipped']++;
                return;
            }

            // Create backup
            $backupPath = $filePath . '.backup';
            copy($filePath, $backupPath);

            // Process image
            $tempPath = $filePath . '.tmp';
            if ($this->imageProcessor->processImage($filePath, $tempPath)) {
                // Replace original with optimized version
                if (rename($tempPath, $filePath)) {
                    $newSize = filesize($filePath);
                    $saved = $originalSize - $newSize;
                    
                    echo "✅ Optimized (" . $this->formatBytes($newSize) . ", saved " . $this->formatBytes($saved) . ")\n";
                    
                    $this->stats['processed']++;
                    $this->stats['saved_space'] += $saved;
                    
                    // Create thumbnail
                    $this->createThumbnail($filePath);
                    
                    // Remove backup
                    unlink($backupPath);
                } else {
                    echo "❌ Failed to replace original\n";
                    $this->stats['errors']++;
                    // Restore backup
                    rename($backupPath, $filePath);
                }
            } else {
                echo "❌ Processing failed\n";
                $this->stats['errors']++;
                // Restore backup
                rename($backupPath, $filePath);
            }
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            $this->stats['errors']++;
        }
    }

    /**
     * Create thumbnail for image
     */
    private function createThumbnail($filePath)
    {
        $filename = basename($filePath);
        $thumbPath = $this->thumbnailsDir . $filename;
        
        // Skip if thumbnail already exists
        if (file_exists($thumbPath)) {
            return;
        }

        try {
            $this->imageProcessor->createThumbnail($filePath, $thumbPath);
        } catch (Exception $e) {
            // Silently fail for thumbnails
        }
    }

    /**
     * Clean up old files
     */
    public function cleanup($maxAge = 86400) // 24 hours
    {
        echo "🧹 Cleaning up old files...\n";
        
        $cleaned = $this->imageProcessor->cleanupOldFiles($this->uploadsDir, $maxAge);
        $cleaned += $this->imageProcessor->cleanupOldFiles($this->thumbnailsDir, $maxAge);
        
        echo "🗑️  Cleaned up {$cleaned} old files\n";
    }

    /**
     * Display optimization statistics
     */
    private function displayStats()
    {
        echo "\n📊 Optimization Complete!\n";
        echo "========================\n";
        echo "✅ Processed: {$this->stats['processed']} images\n";
        echo "⏭️  Skipped: {$this->stats['skipped']} images\n";
        echo "❌ Errors: {$this->stats['errors']} images\n";
        echo "💾 Space saved: " . $this->formatBytes($this->stats['saved_space']) . "\n";
        
        if ($this->stats['processed'] > 0) {
            $avgSaved = $this->stats['saved_space'] / $this->stats['processed'];
            echo "📈 Average saved per image: " . $this->formatBytes($avgSaved) . "\n";
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

// Run the optimizer
if (php_sapi_name() === 'cli') {
    $optimizer = new ImageOptimizer();
    
    $command = $argv[1] ?? 'optimize';
    
    switch ($command) {
        case 'optimize':
            $optimizer->optimizeAll();
            break;
        case 'cleanup':
            $maxAge = isset($argv[2]) ? (int)$argv[2] : 86400;
            $optimizer->cleanup($maxAge);
            break;
        case 'help':
            echo "Image Optimizer\n";
            echo "==============\n";
            echo "Usage: php optimize-images.php [command]\n\n";
            echo "Commands:\n";
            echo "  optimize  - Optimize all images (default)\n";
            echo "  cleanup   - Clean up old files\n";
            echo "  help      - Show this help\n\n";
            echo "Examples:\n";
            echo "  php optimize-images.php optimize\n";
            echo "  php optimize-images.php cleanup 3600  # Clean files older than 1 hour\n";
            break;
        default:
            echo "Unknown command: {$command}\n";
            echo "Use 'php optimize-images.php help' for usage information.\n";
    }
} else {
    echo "This script must be run from the command line.\n";
}
