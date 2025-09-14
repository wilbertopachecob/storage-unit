<?php
function isFileIncluded(string $fileA): bool
{
    $includedFiles = get_included_files();
    $files = array_map(function ($val) {
        $file = basename($val);
        return $file;
    }, $includedFiles);

    return in_array($fileA, $files);
}

function unsetVariables(array $vars): void{
    foreach($vars as $var){
        unset($var);
    }
}

function generateCSRFToken() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password) {
    return strlen($password) >= 8;
}
