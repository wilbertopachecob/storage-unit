# Storage Unit Management System - Test Suite

## Overview

This test suite provides comprehensive coverage for the Storage Unit Management System, including the new features implemented as part of the quick wins initiative.

## Test Structure

```
tests/
├── TestCase.php                    # Base test case with common functionality
├── Unit/                          # Unit tests for individual components
│   ├── Models/                    # Model tests
│   │   ├── CategoryTest.php       # Category model tests
│   │   ├── ItemTest.php          # Item model tests (updated)
│   │   ├── LocationTest.php      # Location model tests
│   │   └── UserTest.php          # User model tests
│   └── Controllers/              # Controller tests
│       ├── CategoryControllerTest.php      # Category controller tests
│       ├── EnhancedItemControllerTest.php  # Enhanced item controller tests
│       ├── LocationControllerTest.php      # Location controller tests
│       └── ItemControllerTest.php         # Item controller tests
├── Feature/                       # Feature tests (end-to-end)
└── Integration/                   # Integration tests
```

## New Features Tested

### 1. Categories System
- **Model Tests** (`CategoryTest.php`):
  - CRUD operations (Create, Read, Update, Delete)
  - Data validation (name, color, icon)
  - User association and security
  - Item count tracking
  - Name uniqueness validation

- **Controller Tests** (`CategoryControllerTest.php`):
  - HTTP request handling
  - CSRF protection
  - Error handling and validation
  - Authentication requirements

### 2. Locations System
- **Model Tests** (`LocationTest.php`):
  - CRUD operations with hierarchical support
  - Parent-child relationships
  - Full path generation
  - Hierarchy building
  - Item count tracking
  - Name uniqueness within parent scope

- **Controller Tests** (`LocationControllerTest.php`):
  - HTTP request handling
  - Circular reference prevention
  - Parent validation
  - Authentication requirements

### 3. Enhanced Item Management
- **Model Tests** (Updated `ItemTest.php`):
  - Category and location associations
  - Enhanced search with filters
  - Multi-criteria filtering
  - Detailed item retrieval with relationships

- **Controller Tests** (`EnhancedItemControllerTest.php`):
  - Advanced search functionality
  - Category and location filtering
  - Analytics and reporting
  - Enhanced CRUD operations

## Running Tests

### Run All Tests
```bash
composer test
```

### Run New Feature Tests Only
```bash
./scripts/run-new-tests.sh
```

### Run Specific Test Suites
```bash
# Category tests
vendor/bin/phpunit tests/Unit/Models/CategoryTest.php
vendor/bin/phpunit tests/Unit/Controllers/CategoryControllerTest.php

# Location tests
vendor/bin/phpunit tests/Unit/Models/LocationTest.php
vendor/bin/phpunit tests/Unit/Controllers/LocationControllerTest.php

# Enhanced Item tests
vendor/bin/phpunit tests/Unit/Controllers/EnhancedItemControllerTest.php
```

### Run with Coverage
```bash
composer test-coverage
```

## Test Database Setup

The test suite uses a separate test database (`storageunit_test`) with the following setup:

### Test Data Structure
- **Users**: 2 test users (ID 1 and 2)
- **Categories**: 3 categories for user 1, 1 category for user 2
- **Locations**: 3 locations for user 1, 1 location for user 2
- **Items**: 2 items for user 1, 1 item for user 2
- **Tags**: 4 tags for user 1
- **Item Tags**: Relationships between items and tags

### Database Reset
Each test method runs with a clean database state, ensuring test isolation and reliability.

## Test Coverage

### Model Tests
- ✅ Constructor and property initialization
- ✅ CRUD operations (Create, Read, Update, Delete)
- ✅ Data validation and sanitization
- ✅ Relationship management
- ✅ Security and user isolation
- ✅ Error handling and exceptions

### Controller Tests
- ✅ HTTP request processing
- ✅ Authentication and authorization
- ✅ CSRF protection
- ✅ Input validation
- ✅ Error handling and user feedback
- ✅ Response formatting

### Integration Tests
- ✅ Database operations
- ✅ Model-controller interactions
- ✅ Session management
- ✅ File upload handling

## Test Patterns

### 1. Data Validation Tests
```php
public function testCreateWithInvalidData()
{
    $item = new Item('', 'Description', 1, 1); // Empty title
    
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Invalid item data');
    $item->create();
}
```

### 2. Authentication Tests
```php
public function testIndexThrowsExceptionWhenNotAuthenticated()
{
    $this->clearSession();
    
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('User not authenticated');
    $this->controller->index();
}
```

### 3. Relationship Tests
```php
public function testGetWithItemCount()
{
    $categories = Category::getWithItemCount(1);
    
    $this->assertIsArray($categories);
    $this->assertCount(3, $categories);
    
    foreach ($categories as $category) {
        $this->assertArrayHasKey('item_count', $category);
    }
}
```

## Mocking and Test Utilities

### TestCase Base Class
The `TestCase` class provides:
- Database setup and teardown
- Test data creation helpers
- Session management
- Authentication simulation

### Helper Methods
- `createTestUser()` - Create test users
- `createTestItem()` - Create test items with relationships
- `createTestCategory()` - Create test categories
- `createTestLocation()` - Create test locations
- `createTestTag()` - Create test tags
- `authenticateUser()` - Simulate user authentication
- `clearSession()` - Clear session data

## Best Practices

### 1. Test Isolation
- Each test runs with a clean database
- No test depends on another test's data
- Proper setup and teardown

### 2. Descriptive Test Names
- Test names clearly describe what is being tested
- Include expected behavior in the name
- Group related tests logically

### 3. Comprehensive Coverage
- Test both success and failure scenarios
- Test edge cases and boundary conditions
- Test security and validation

### 4. Maintainable Tests
- Use helper methods for common operations
- Keep tests focused and single-purpose
- Avoid complex test logic

## Continuous Integration

The test suite is designed to run in CI/CD pipelines:

1. **Database Setup**: Tests use a dedicated test database
2. **Environment Variables**: Proper environment configuration
3. **Exit Codes**: Tests return proper exit codes for CI
4. **Coverage Reports**: Generate coverage reports for analysis

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Ensure test database exists
   - Check database credentials
   - Verify database permissions

2. **Test Data Issues**
   - Check test data setup in `TestCase::resetDatabase()`
   - Verify foreign key constraints
   - Ensure proper test isolation

3. **Authentication Errors**
   - Check session management in tests
   - Verify user authentication simulation
   - Ensure proper session clearing

### Debug Mode
Run tests with verbose output for debugging:
```bash
vendor/bin/phpunit --verbose tests/Unit/Models/CategoryTest.php
```

## Future Enhancements

### Planned Test Improvements
- [ ] API endpoint testing
- [ ] Performance testing
- [ ] Security testing
- [ ] Mobile app testing
- [ ] Integration with external services

### Test Coverage Goals
- [ ] 90%+ code coverage
- [ ] 100% critical path coverage
- [ ] Full API coverage
- [ ] Complete user workflow coverage

---

**Note**: This test suite is continuously updated as new features are added to the system. Always run tests before deploying changes to ensure system stability.
