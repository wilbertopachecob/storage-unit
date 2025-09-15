<?php
/**
 * API Tests Runner
 * Runs all API-related tests
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;

// Add test files to the suite
$suite = new TestSuite('Storage Unit API Tests');

// Unit Tests
$suite->addTestFile(__DIR__ . '/Unit/Models/ItemTest.php');
$suite->addTestFile(__DIR__ . '/Unit/Models/CategoryTest.php');
$suite->addTestFile(__DIR__ . '/Unit/Models/LocationTest.php');
$suite->addTestFile(__DIR__ . '/Unit/Models/UserTest.php');
$suite->addTestFile(__DIR__ . '/Unit/Core/ApiResponseTest.php');
$suite->addTestFile(__DIR__ . '/Unit/Controllers/ApiControllerTest.php');

// Integration Tests
$suite->addTestFile(__DIR__ . '/Integration/ApiEndpointsTest.php');

// Run the tests
$runner = new TestRunner();
$result = $runner->run($suite);

// Exit with appropriate code
exit($result->wasSuccessful() ? 0 : 1);
?>
