<?php
/**
 * Authentication Controller
 * Handles user authentication, registration, and logout
 */

namespace StorageUnit\Controllers;

use StorageUnit\Models\User;
use StorageUnit\Core\Security;

class AuthController
{
    /**
     * Handle user registration
     */
    public function register()
    {
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid security token';
            } else {
                // Sanitize input
                $name = Security::sanitizeInput($_POST['name'] ?? '');
                $email = Security::sanitizeInput($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                // Validate input
                if (empty($name)) {
                    $errors[] = 'Name is required';
                }

                if (empty($email) || !Security::validateEmail($email)) {
                    $errors[] = 'Valid email is required';
                }

                if (empty($password) || !Security::validatePassword($password)) {
                    $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long';
                }

                if (empty($errors)) {
                    try {
                        $user = new User($email, $password, $name);
                        if ($user->create()) {
                            $success = true;
                            // Redirect to dashboard after successful registration
                            header('Location: ' . BASE_URL . '/index.php?script=itemsList');
                            exit;
                        } else {
                            $errors[] = 'Registration failed. Please try again.';
                        }
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }
        }

        return [
            'errors' => $errors,
            'success' => $success,
            'csrf_token' => Security::generateCSRFToken()
        ];
    }

    /**
     * Handle user login
     */
    public function login()
    {
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid security token';
            } else {
                // Sanitize input
                $email = Security::sanitizeInput($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                // Validate input
                if (empty($email) || !Security::validateEmail($email)) {
                    $errors[] = 'Valid email is required';
                }

                if (empty($password)) {
                    $errors[] = 'Password is required';
                }

                if (empty($errors)) {
                    try {
                        $user = new User($email, $password);
                        if ($user->authenticate()) {
                            $success = true;
                            // Redirect to dashboard after successful login
                            header('Location: ' . BASE_URL . '/index.php?script=itemsList');
                            exit;
                        } else {
                            $errors[] = 'Invalid email or password';
                        }
                    } catch (\Exception $e) {
                        $errors[] = 'Login failed. Please try again.';
                    }
                }
            }
        }

        return [
            'errors' => $errors,
            'success' => $success,
            'csrf_token' => Security::generateCSRFToken()
        ];
    }

    /**
     * Handle user logout
     */
    public function logout()
    {
        User::logout();
        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated()
    {
        return User::isLoggedIn();
    }

    /**
     * Get current user
     */
    public function getCurrentUser()
    {
        return User::getCurrentUser();
    }
}
