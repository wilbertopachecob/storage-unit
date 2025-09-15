# Storage Unit API Tests

This directory contains comprehensive unit and integration tests for the Storage Unit API.

## Test Structure

```
tests/
├── Unit/                          # Unit tests for individual components
│   ├── Models/                    # Model tests
│   │   ├── ItemTest.php          # Item model tests
│   │   ├── CategoryTest.php      # Category model tests
│   │   ├── LocationTest.php      # Location model tests
│   │   └── UserTest.php          # User model tests
│   ├── Controllers/               # Controller tests
│   │   └── ApiControllerTest.php # API controller base class tests
│   └── Core/                     # Core functionality tests
│       └── ApiResponseTest.php   # API response handler tests
├── Integration/                   # Integration tests
│   └── ApiEndpointsTest.php      # API endpoint tests
├── Helpers/                       # Test utilities
│   └── TestHelper.php            # Common test helper functions
├── run_api_tests.php             # Test runner script
└── README.md                     # This file
```

## Running Tests

### Prerequisites

1. Install PHPUnit:
```bash
composer install
```

2. Ensure your database is set up for testing (optional for unit tests)

### Running All Tests

```bash
# Using PHPUnit directly
./vendor/bin/phpunit --configuration phpunit-api.xml

# Using the test runner script
php tests/run_api_tests.php

# Using Composer (if configured)
composer test:api
```

### Running Specific Test Suites

```bash
# Unit tests only
./vendor/bin/phpunit --configuration phpunit-api.xml --testsuite "API Unit Tests"

# Integration tests only
./vendor/bin/phpunit --configuration phpunit-api.xml --testsuite "API Integration Tests"

# Specific test file
./vendor/bin/phpunit tests/Unit/Models/ItemTest.php

# Specific test method
./vendor/bin/phpunit --filter testItemCreation tests/Unit/Models/ItemTest.php
```

### Running with Coverage

```bash
# Generate HTML coverage report
./vendor/bin/phpunit --configuration phpunit-api.xml --coverage-html tests/coverage/html

# Generate text coverage report
./vendor/bin/phpunit --configuration phpunit-api.xml --coverage-text
```

## Test Categories

### Unit Tests

#### Model Tests
- **ItemTest.php**: Tests for Item model CRUD operations, validation, and data retrieval
- **CategoryTest.php**: Tests for Category model functionality and item counting
- **LocationTest.php**: Tests for Location model with coordinates support
- **UserTest.php**: Tests for User model authentication and profile management

#### Controller Tests
- **ApiControllerTest.php**: Tests for base API controller functionality

#### Core Tests
- **ApiResponseTest.php**: Tests for API response formatting and HTTP status codes

### Integration Tests

#### API Endpoint Tests
- **ApiEndpointsTest.php**: Tests for all API endpoints including:
  - Items API (GET, POST, PUT, PATCH, DELETE)
  - Categories API (GET, POST, PUT, PATCH, DELETE)
  - Locations API (GET, POST, PUT, PATCH, DELETE)
  - Analytics API (GET)
  - Users API (GET, PUT, PATCH, POST)
  - Authentication API (POST)

## Test Coverage

The tests aim to achieve high coverage of:

- **Models**: All CRUD operations, validation, and data retrieval methods
- **Controllers**: Request handling, validation, and response formatting
- **API Endpoints**: All HTTP methods and response scenarios
- **Error Handling**: Validation errors, authentication failures, and server errors
- **Edge Cases**: Invalid input, missing data, and boundary conditions

## Test Data

### Mock Data
Tests use mock data to avoid database dependencies:
- **Items**: Test items with various properties
- **Categories**: Test categories with colors and icons
- **Locations**: Test locations with coordinates
- **Users**: Test users with authentication data
- **Analytics**: Test analytics data with statistics

### Test Helper
The `TestHelper` class provides utilities for:
- Creating mock database connections
- Generating test data
- Mocking HTTP requests
- Asserting API response structure
- Managing test sessions

## Writing New Tests

### Unit Test Example

```php
<?php
namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use StorageUnit\Models\Item;

class ItemTest extends TestCase
{
    public function testItemCreation()
    {
        $item = new Item('Test Item', 'Description', 1, 1);
        
        $this->assertEquals('Test Item', $item->getTitle());
        $this->assertEquals('Description', $item->getDescription());
        $this->assertEquals(1, $item->getQty());
    }
}
```

### Integration Test Example

```php
<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class ApiEndpointsTest extends TestCase
{
    public function testItemsApiGetAll()
    {
        // Mock database and authentication
        $this->mockUserAuthentication();
        
        // Test API endpoint
        $response = $this->makeApiRequest('GET', '/items');
        
        $this->assertEquals(200, $response['code']);
        $this->assertTrue($response['data']['success']);
    }
}
```

## Test Configuration

### PHPUnit Configuration
The `phpunit-api.xml` file configures:
- Test suites and directories
- Coverage reporting
- Test execution settings
- Source code inclusion/exclusion

### Coverage Reports
Coverage reports are generated in:
- **HTML**: `tests/coverage/html/`
- **Text**: `tests/coverage/coverage.txt`
- **XML**: `tests/coverage/coverage.xml`

## Continuous Integration

### GitHub Actions Example

```yaml
name: API Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: ./vendor/bin/phpunit --configuration phpunit-api.xml
      - name: Upload coverage
        uses: codecov/codecov-action@v1
        with:
          file: tests/coverage/coverage.xml
```

## Best Practices

### Test Organization
- Group related tests in the same file
- Use descriptive test method names
- Follow the AAA pattern (Arrange, Act, Assert)

### Mocking
- Mock external dependencies (database, HTTP requests)
- Use realistic test data
- Test both success and failure scenarios

### Assertions
- Use specific assertions (assertEquals vs assertTrue)
- Test both positive and negative cases
- Verify error messages and status codes

### Test Data
- Use factories or builders for complex test data
- Keep test data minimal and focused
- Avoid hardcoded values when possible

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Ensure database is running
   - Check connection credentials
   - Use mock database for unit tests

2. **Session Issues**
   - Clear session data between tests
   - Mock session data when needed

3. **File Permission Errors**
   - Ensure test directories are writable
   - Check coverage report directory permissions

4. **Memory Issues**
   - Increase memory limit in phpunit.xml
   - Use data providers for large datasets

### Debugging Tests

```bash
# Run with verbose output
./vendor/bin/phpunit --configuration phpunit-api.xml --verbose

# Stop on first failure
./vendor/bin/phpunit --configuration phpunit-api.xml --stop-on-failure

# Run specific test with debug output
./vendor/bin/phpunit --configuration phpunit-api.xml --filter testItemCreation --verbose
```

## Contributing

When adding new tests:

1. Follow the existing naming conventions
2. Add tests for both success and failure scenarios
3. Update this README if adding new test categories
4. Ensure tests are isolated and don't depend on each other
5. Add appropriate documentation for complex test scenarios