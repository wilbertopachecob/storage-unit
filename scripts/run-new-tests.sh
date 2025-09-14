#!/bin/bash

# Run tests for new features
# This script runs unit tests for the new Category, Location, and EnhancedItemController features

echo "🧪 Running Unit Tests for New Features"
echo "======================================"

# Set up test environment
export APP_ENV=testing
export DB_NAME=storageunit_test

# Run Category Model Tests
echo ""
echo "📁 Testing Category Model..."
vendor/bin/phpunit tests/Unit/Models/CategoryTest.php --verbose

# Run Location Model Tests
echo ""
echo "📍 Testing Location Model..."
vendor/bin/phpunit tests/Unit/Models/LocationTest.php --verbose

# Run Enhanced Item Controller Tests
echo ""
echo "🔧 Testing Enhanced Item Controller..."
vendor/bin/phpunit tests/Unit/Controllers/EnhancedItemControllerTest.php --verbose

# Run Category Controller Tests
echo ""
echo "📂 Testing Category Controller..."
vendor/bin/phpunit tests/Unit/Controllers/CategoryControllerTest.php --verbose

# Run Location Controller Tests
echo ""
echo "🏠 Testing Location Controller..."
vendor/bin/phpunit tests/Unit/Controllers/LocationControllerTest.php --verbose

# Run Updated Item Model Tests
echo ""
echo "📦 Testing Updated Item Model..."
vendor/bin/phpunit tests/Unit/Models/ItemTest.php --verbose

echo ""
echo "✅ All new feature tests completed!"
echo ""
echo "📊 Test Summary:"
echo "- Category Model: CRUD operations, validation, relationships"
echo "- Location Model: CRUD operations, hierarchy, validation"
echo "- Enhanced Item Controller: Search, filtering, analytics"
echo "- Category Controller: Management operations"
echo "- Location Controller: Management operations"
echo "- Updated Item Model: New fields, enhanced search"
