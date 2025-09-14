<?php
/**
 * Security Helper Class
 * Handles CSRF protection, input validation, and sanitization
 */

namespace StorageUnit\Core;

class Security
{
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token)
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        // Check token lifetime
        $lifetime = defined('CSRF_TOKEN_LIFETIME') ? CSRF_TOKEN_LIFETIME : 3600;
        if (time() - $_SESSION['csrf_token_time'] > $lifetime) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize input data
     */
    public static function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email address
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     */
    public static function validatePassword($password)
    {
        $minLength = defined('PASSWORD_MIN_LENGTH') ? PASSWORD_MIN_LENGTH : 8;
        return strlen($password) >= $minLength;
    }

    /**
     * Generate secure random string
     */
    public static function generateRandomString($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Hash password securely
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password against hash
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = null, $maxSize = null)
    {
        $allowedTypes = $allowedTypes ?? UPLOAD_ALLOWED_TYPES;
        $maxSize = $maxSize ?? UPLOAD_MAX_SIZE;

        $errors = [];

        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'No file uploaded';
            return $errors;
        }

        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }

        // Check file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            $errors[] = 'File type not allowed';
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/jpg'
        ];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            $errors[] = 'Invalid file type';
        }

        return $errors;
    }

    /**
     * Generate secure filename
     */
    public static function generateSecureFilename($originalName)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . self::generateRandomString(8) . '.' . $extension;
    }
}
