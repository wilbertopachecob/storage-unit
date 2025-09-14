<?php
/**
 * Logger Factory
 * Creates logger instances based on configuration
 */

namespace StorageUnit\Core;

class LoggerFactory
{
    private static $loggers = [];
    private static $defaultLogger = null;

    /**
     * Get a logger instance
     */
    public static function getLogger(string $name = 'default'): LoggerInterface
    {
        if (!isset(self::$loggers[$name])) {
            self::$loggers[$name] = self::createLogger($name);
        }

        return self::$loggers[$name];
    }

    /**
     * Get the default logger
     */
    public static function getDefaultLogger(): LoggerInterface
    {
        if (self::$defaultLogger === null) {
            self::$defaultLogger = self::getLogger('default');
        }

        return self::$defaultLogger;
    }

    /**
     * Create a new logger instance
     */
    private static function createLogger(string $name): LoggerInterface
    {
        // Load configuration
        $config = require __DIR__ . '/../../config/app/config.php';
        $loggerConfig = $config['logging'][$name] ?? $config['logging']['default'] ?? [];

        $type = $loggerConfig['type'] ?? 'file';
        $logPath = $loggerConfig['path'] ?? null;
        $maxFileSize = $loggerConfig['max_file_size'] ?? 10485760; // 10MB
        $maxFiles = $loggerConfig['max_files'] ?? 5;

        switch ($type) {
            case 'file':
                return new FileLogger($logPath, $maxFileSize, $maxFiles);
            
            case 'rollbar':
                // Future implementation for Rollbar
                // return new RollbarLogger($loggerConfig);
                throw new \Exception("Rollbar logger not implemented yet");
            
            case 'slack':
                // Future implementation for Slack
                // return new SlackLogger($loggerConfig);
                throw new \Exception("Slack logger not implemented yet");
            
            default:
                throw new \Exception("Unknown logger type: {$type}");
        }
    }

    /**
     * Register a custom logger
     */
    public static function registerLogger(string $name, LoggerInterface $logger): void
    {
        self::$loggers[$name] = $logger;
    }

    /**
     * Set the default logger
     */
    public static function setDefaultLogger(LoggerInterface $logger): void
    {
        self::$defaultLogger = $logger;
    }

    /**
     * Clear all logger instances
     */
    public static function clearLoggers(): void
    {
        self::$loggers = [];
        self::$defaultLogger = null;
    }
}
