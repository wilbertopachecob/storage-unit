<?php
// User class is now autoloaded, no need for manual includes
$errors = [];
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$baseUrl = $protocol . "://" . $host;
//I create this file to handle the redirections after the sings because you cant
//send headers after the code
//Handling singOut
// Check for sign parameter in both GET and POST
$signParam = $_GET['sign'] ?? $_POST['sign'] ?? null;

if (isset($signParam)) {
    if ($signParam == 'out') {
        \StorageUnit\Models\User::logout();
        header("Location: " . $baseUrl . "/index.php");
        exit;
    }

//Handling signIn
    if ($signParam == 'in') {
        if (isset($_POST['btn_submit'])) {
            $password = $_POST['password'];
            $email = $_POST['email'];
            
            // Basic validation
            if (empty($email) || empty($password)) {
                $_SESSION['login_error'] = 'Please fill in all fields.';
                header("Location: " . $baseUrl . "/signIn.php");
                exit;
            }
            
            $user = new \StorageUnit\Models\User(null, $email, $password);
            try {
                $result = $user->login();
                
                // Debug logging
                error_log("Login attempt - Email: $email, Result: " . var_export($result, true));
                error_log("Session before login: " . print_r($_SESSION, true));
                
                if ($result) {
                    // Login successful - clear any existing errors
                    unset($_SESSION['login_error']);
                    $_SESSION['login_success'] = 'Welcome back!';
                    
                    error_log("Login successful - redirecting to items list");
                    // Redirect to items list
                    header("Location: " . $baseUrl . "/index.php?script=itemsList");
                    exit;
                } else {
                    error_log("Login failed - result was false");
                    $_SESSION['login_error'] = 'Invalid email or password.';
                    header("Location: " . $baseUrl . "/signIn.php");
                    exit;
                }
            } catch (Exception $e) {
                error_log("Login exception: " . $e->getMessage());
                $_SESSION['login_error'] = 'Login failed: ' . $e->getMessage();
                header("Location: " . $baseUrl . "/signIn.php");
                exit;
            }
        }
    }

//Handling signUp
    if ($signParam == 'up') {
        if (isset($_POST['btn_submit'])) {
            $password = $_POST['password'];
            $email = $_POST['email'];
            $name = $_POST['name'];
            
            // Basic validation
            if (empty($name) || empty($email) || empty($password)) {
                $_SESSION['signup_error'] = 'Please fill in all fields.';
                header("Location: " . $baseUrl . "/signUp.php");
                exit;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['signup_error'] = 'Please enter a valid email address.';
                header("Location: " . $baseUrl . "/signUp.php");
                exit;
            }
            
            if (strlen($password) < 8) {
                $_SESSION['signup_error'] = 'Password must be at least 8 characters long.';
                header("Location: " . $baseUrl . "/signUp.php");
                exit;
            }
            
            $user = new \StorageUnit\Models\User($email, $password, $name);
            try {
                $result = $user->addUser();
                if ($result) {
                    // Signup successful
                    $_SESSION['signup_success'] = 'Account created successfully! Welcome!';
                    header("Location: " . $baseUrl . "/index.php?script=itemsList");
                    exit;
                } else {
                    $_SESSION['signup_error'] = 'Failed to create account. Please try again.';
                    header("Location: " . $baseUrl . "/signUp.php");
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['signup_error'] = 'Signup failed: ' . $e->getMessage();
                header("Location: " . $baseUrl . "/signUp.php");
                exit;
            }
        }
    }

}
