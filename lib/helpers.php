<?php
if (!function_exists('isFileIncluded')) {
    function isFileIncluded(string $fileA): bool
    {
        $includedFiles = get_included_files();
        $files = array_map(function ($val) {
            $file = basename($val);
            return $file;
        }, $includedFiles);

        return in_array($fileA, $files);
    }
}

if (!function_exists('unsetVariables')) {
    function unsetVariables(array $vars): void{
        foreach($vars as $var){
            unset($var);
        }
    }
}

if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token) {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('validateEmail')) {
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('validatePassword')) {
    function validatePassword($password) {
        return strlen($password) >= 8;
    }
}
