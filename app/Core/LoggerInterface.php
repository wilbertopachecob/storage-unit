<?php
/**
 * Logger Interface
 * Defines the contract for all logger implementations
 */

namespace StorageUnit\Core;

interface LoggerInterface
{
    /**
     * Log an emergency message
     * System is unusable
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * Log an alert message
     * Action must be taken immediately
     */
    public function alert(string $message, array $context = []): void;

    /**
     * Log a critical message
     * Critical conditions
     */
    public function critical(string $message, array $context = []): void;

    /**
     * Log an error message
     * Runtime errors that do not require immediate action
     */
    public function error(string $message, array $context = []): void;

    /**
     * Log a warning message
     * Exceptional occurrences that are not errors
     */
    public function warning(string $message, array $context = []): void;

    /**
     * Log a notice message
     * Normal but significant condition
     */
    public function notice(string $message, array $context = []): void;

    /**
     * Log an info message
     * Interesting events
     */
    public function info(string $message, array $context = []): void;

    /**
     * Log a debug message
     * Detailed information for debugging
     */
    public function debug(string $message, array $context = []): void;

    /**
     * Log a message with a specific level
     */
    public function log(string $level, string $message, array $context = []): void;
}
