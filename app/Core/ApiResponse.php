<?php
/**
 * API Response Handler
 * Provides consistent API response formatting
 */

namespace StorageUnit\Core;

class ApiResponse
{
    /**
     * Send a successful response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'code' => $statusCode,
            'timestamp' => date('c')
        ]);
        exit;
    }

    /**
     * Send an error response
     *
     * @param string $message
     * @param int $statusCode
     * @param string $error
     * @param array $details
     * @return void
     */
    public static function error(string $message, int $statusCode = 400, string $error = 'Bad Request', array $details = []): void
    {
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'error' => $error,
            'message' => $message,
            'code' => $statusCode,
            'details' => $details,
            'timestamp' => date('c')
        ]);
        exit;
    }

    /**
     * Send a validation error response
     *
     * @param array $errors
     * @param string $message
     * @return void
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): void
    {
        self::error($message, 422, 'Validation Error', $errors);
    }

    /**
     * Send a not found response
     *
     * @param string $message
     * @return void
     */
    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, 404, 'Not Found');
    }

    /**
     * Send an unauthorized response
     *
     * @param string $message
     * @return void
     */
    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error($message, 401, 'Unauthorized');
    }

    /**
     * Send a forbidden response
     *
     * @param string $message
     * @return void
     */
    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::error($message, 403, 'Forbidden');
    }

    /**
     * Send a method not allowed response
     *
     * @param string $message
     * @return void
     */
    public static function methodNotAllowed(string $message = 'Method not allowed'): void
    {
        self::error($message, 405, 'Method Not Allowed');
    }

    /**
     * Send a created response
     *
     * @param mixed $data
     * @param string $message
     * @return void
     */
    public static function created($data = null, string $message = 'Resource created successfully'): void
    {
        self::success($data, $message, 201);
    }

    /**
     * Send a no content response
     *
     * @return void
     */
    public static function noContent(): void
    {
        http_response_code(204);
        exit;
    }
}
?>
