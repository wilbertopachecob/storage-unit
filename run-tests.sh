#!/bin/bash

# Storage Unit Management System - Test Runner
# This script sets up and runs the complete test suite

echo "🧪 Storage Unit Management System - Test Suite"
echo "=============================================="

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP first."
    exit 1
fi

# Check if MySQL is running
if ! pgrep -x "mysqld" > /dev/null; then
    echo "⚠️  MySQL is not running. Please start MySQL first."
    echo "   On macOS: brew services start mysql"
    echo "   On Ubuntu: sudo systemctl start mysql"
    echo "   On Windows: Start MySQL service"
fi

echo "📦 Installing dependencies..."
composer install --no-interaction

if [ $? -ne 0 ]; then
    echo "❌ Failed to install dependencies"
    exit 1
fi

echo "✅ Dependencies installed successfully"

# Check if test database exists
echo "🗄️  Setting up test database..."

# Create test database if it doesn't exist
mysql -u root -e "CREATE DATABASE IF NOT EXISTS storageunit_test;" 2>/dev/null

if [ $? -ne 0 ]; then
    echo "⚠️  Could not create test database. Please ensure MySQL is running and accessible."
    echo "   You may need to run: mysql -u root -e 'CREATE DATABASE storageunit_test;'"
fi

# Import test schema
if [ -f "tests/database_test.sql" ]; then
    mysql -u root storageunit_test < tests/database_test.sql 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "✅ Test database schema imported"
    else
        echo "⚠️  Could not import test schema. Please run manually:"
        echo "   mysql -u root storageunit_test < tests/database_test.sql"
    fi
fi

echo ""
echo "🚀 Running tests..."
echo "=================="

# Run tests with different configurations
echo ""
echo "📋 Running Unit Tests..."
./vendor/bin/phpunit --testsuite Unit --verbose

echo ""
echo "🔗 Running Integration Tests..."
./vendor/bin/phpunit --testsuite Integration --verbose

echo ""
echo "🎯 Running Feature Tests..."
./vendor/bin/phpunit --testsuite Feature --verbose

echo ""
echo "📊 Running All Tests with Coverage..."
./vendor/bin/phpunit --coverage-html coverage --coverage-text

echo ""
echo "✅ Test suite completed!"
echo ""
echo "📈 Coverage report generated in: coverage/index.html"
echo "📄 Text coverage report: coverage.txt"
echo ""
echo "🎉 All tests completed successfully!"
