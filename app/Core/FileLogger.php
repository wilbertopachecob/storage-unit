<?php
/**
 * File Logger Implementation
 * Logs messages to files with rotation support
 */

namespace StorageUnit\Core;

class FileLogger implements LoggerInterface
{
    private $logPath;
    private $maxFileSize;
    private $maxFiles;
    private $dateFormat;

    public function __construct(string $logPath = null, int $maxFileSize = 10485760, int $maxFiles = 5)
    {
        $this->logPath = $logPath ?: __DIR__ . '/../../storage/logs/app.log';
        $this->maxFileSize = $maxFileSize; // 10MB default
        $this->maxFiles = $maxFiles;
        $this->dateFormat = 'Y-m-d H:i:s';
        
        // Ensure log directory exists
        $this->ensureLogDirectory();
    }

    /**
     * Log an emergency message
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }

    /**
     * Log an alert message
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    /**
     * Log a critical message
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Log an error message
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log a warning message
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log a notice message
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    /**
     * Log an info message
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log a debug message
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Log a message with a specific level
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date($this->dateFormat);
        $contextString = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextString}" . PHP_EOL;

        // Check if we need to rotate the log file
        if (file_exists($this->logPath) && filesize($this->logPath) > $this->maxFileSize) {
            $this->rotateLogFile();
        }

        // Write to log file
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Ensure log directory exists
     */
    private function ensureLogDirectory(): void
    {
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Rotate log file when it gets too large
     */
    private function rotateLogFile(): void
    {
        // Move existing log files
        for ($i = $this->maxFiles - 1; $i > 0; $i--) {
            $oldFile = $this->logPath . '.' . $i;
            $newFile = $this->logPath . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                if ($i === $this->maxFiles - 1) {
                    unlink($oldFile); // Delete oldest file
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }

        // Move current log to .1
        if (file_exists($this->logPath)) {
            rename($this->logPath, $this->logPath . '.1');
        }
    }

    /**
     * Get the current log file path
     */
    public function getLogPath(): string
    {
        return $this->logPath;
    }

    /**
     * Clear all log files
     */
    public function clearLogs(): void
    {
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }

        for ($i = 1; $i <= $this->maxFiles; $i++) {
            $logFile = $this->logPath . '.' . $i;
            if (file_exists($logFile)) {
                unlink($logFile);
            }
        }
    }
}
