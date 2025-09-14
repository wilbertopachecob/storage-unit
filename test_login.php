<?php
session_start();

echo "=== LOGIN UNIT TEST ===\n";

// Test database connection
try {
    include './lib/db/connection.php';
    $conn = new Connection();
    $conexion = $conn->getConnection();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Test with admin credentials
$email = 'admin@example.com';
$password = 'password123';

echo "\n=== TESTING LOGIN WITH: $email ===\n";

// Check if user exists
include './lib/db/Models/user.php';
$userExists = User::ifExistsEmail($email);
echo "User exists in database: " . ($userExists ? 'YES' : 'NO') . "\n";

if ($userExists) {
    // Try to login
    $user = new User($email, $password, null);
    try {
        $result = $user->login();
        echo "Login result: " . var_export($result, true) . "\n";
        echo "Result type: " . gettype($result) . "\n";
        echo "Result is truthy: " . ($result ? 'YES' : 'NO') . "\n";
        
        if ($result) {
            echo "✓ Login successful!\n";
            echo "Session data:\n";
            foreach ($_SESSION as $key => $value) {
                if (strpos($key, 'user_') === 0) {
                    echo "  $key: $value\n";
                }
            }
        } else {
            echo "✗ Login failed - credentials incorrect\n";
        }
    } catch (Exception $e) {
        echo "✗ Login exception: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ User not found in database\n";
}

echo "\n=== TESTING FAILED LOGIN ===\n";
$wrongUser = new User('wrong@example.com', 'wrongpassword', null);
try {
    $result = $wrongUser->login();
    echo "Failed login result: " . var_export($result, true) . "\n";
    if (!$result) {
        echo "✓ Failed login correctly returns false\n";
    } else {
        echo "✗ Failed login should return false\n";
    }
} catch (Exception $e) {
    echo "✗ Login exception: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING SIGNS HANDLERS (Success Case) ===\n";
// Clear session for clean test
session_destroy();
session_start();

// Simulate successful login POST
$_POST['sign'] = 'in';
$_POST['btn_submit'] = '1';
$_POST['email'] = $email;
$_POST['password'] = $password;

echo "POST data: " . json_encode($_POST) . "\n";

// Capture any output from signsHandlers
ob_start();
include './lib/signsHandlers.php';
$output = ob_get_clean();

echo "SignsHandlers output: " . ($output ? $output : 'None') . "\n";
echo "Session after handlers:\n";
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'user_') === 0 || strpos($key, 'login_') === 0) {
        echo "  $key: $value\n";
    }
}

echo "\n=== TESTING SIGNS HANDLERS (Error Case) ===\n";
// Clear session for clean test
session_destroy();
session_start();

// Simulate failed login POST
$_POST['sign'] = 'in';
$_POST['btn_submit'] = '1';
$_POST['email'] = 'wrong@example.com';
$_POST['password'] = 'wrongpassword';

echo "POST data: " . json_encode($_POST) . "\n";

// Capture any output from signsHandlers
ob_start();
include './lib/signsHandlers.php';
$output = ob_get_clean();

echo "SignsHandlers output: " . ($output ? $output : 'None') . "\n";
echo "Session after handlers:\n";
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'login_') === 0) {
        echo "  $key: $value\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
?>
