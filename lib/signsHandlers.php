<?php
// if (!isFileIncluded('user.php')) {
//     include 'db/user.php';
// }
include 'db/Models/user.php';
$errors = [];
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$URI = "Location: http://" . $host;
//I create this file to handle the redirections after the sings because you cant
//send headers after the code
//Handling singOut
// Check for sign parameter in both GET and POST
$signParam = $_GET['sign'] ?? $_POST['sign'] ?? null;

if (isset($signParam)) {
    if ($signParam == 'out') {
        USER::logout();
        header($URI);
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
                header("Location: " . $URI . "/signin.php");
                exit;
            }
            
            $user = new User($email, $password, null);
            try {
                $result = $user->login();
                if ($result) {
                    // Login successful - clear any existing errors
                    unset($_SESSION['login_error']);
                    $_SESSION['login_success'] = 'Welcome back!';
                    
                    // Redirect to items list
                    header("Location: " . $URI . "/index.php?script=itemsList");
                    exit;
                } else {
                    $_SESSION['login_error'] = 'Invalid email or password.';
                    header("Location: " . $URI . "/signin.php");
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['login_error'] = 'Login failed: ' . $e->getMessage();
                header("Location: " . $URI . "/signin.php");
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
                header("Location: " . $URI . "/signup.php");
                exit;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['signup_error'] = 'Please enter a valid email address.';
                header("Location: " . $URI . "/signup.php");
                exit;
            }
            
            if (strlen($password) < 8) {
                $_SESSION['signup_error'] = 'Password must be at least 8 characters long.';
                header("Location: " . $URI . "/signup.php");
                exit;
            }
            
            $user = new User($email, $password, $name);
            try {
                $result = $user->addUser();
                if ($result) {
                    // Signup successful
                    $_SESSION['signup_success'] = 'Account created successfully! Welcome!';
                    header("Location: " . $URI . "/index.php?script=itemsList");
                    exit;
                } else {
                    $_SESSION['signup_error'] = 'Failed to create account. Please try again.';
                    header("Location: " . $URI . "/signup.php");
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['signup_error'] = 'Signup failed: ' . $e->getMessage();
                header("Location: " . $URI . "/signup.php");
                exit;
            }
        }
    }

}
