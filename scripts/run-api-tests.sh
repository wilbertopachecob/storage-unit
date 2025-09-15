#!/bin/bash

# Storage Unit API Tests Runner
# This script runs all API-related tests with proper configuration

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if PHPUnit is available
check_phpunit() {
    if [ ! -f "vendor/bin/phpunit" ]; then
        print_error "PHPUnit not found. Please run 'composer install' first."
        exit 1
    fi
}

# Check if test configuration exists
check_config() {
    if [ ! -f "phpunit-api.xml" ]; then
        print_error "Test configuration file 'phpunit-api.xml' not found."
        exit 1
    fi
}

# Create necessary directories
create_directories() {
    print_status "Creating test directories..."
    mkdir -p tests/coverage/html
    mkdir -p tests/coverage/text
    mkdir -p tests/results
    print_success "Test directories created"
}

# Run tests with coverage
run_tests_with_coverage() {
    print_status "Running API tests with coverage..."
    ./vendor/bin/phpunit --configuration phpunit-api.xml --coverage-html tests/coverage/html --coverage-text --coverage-clover tests/coverage/coverage.xml
}

# Run tests without coverage
run_tests() {
    print_status "Running API tests..."
    ./vendor/bin/phpunit --configuration phpunit-api.xml
}

# Run specific test suite
run_test_suite() {
    local suite=$1
    print_status "Running $suite tests..."
    ./vendor/bin/phpunit --configuration phpunit-api.xml --testsuite "$suite"
}

# Run specific test file
run_test_file() {
    local file=$1
    print_status "Running tests in $file..."
    ./vendor/bin/phpunit --configuration phpunit-api.xml "$file"
}

# Run tests with filter
run_test_filter() {
    local filter=$1
    print_status "Running tests matching filter: $filter..."
    ./vendor/bin/phpunit --configuration phpunit-api.xml --filter "$filter"
}

# Show help
show_help() {
    echo "Storage Unit API Tests Runner"
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -h, --help              Show this help message"
    echo "  -c, --coverage          Run tests with coverage report"
    echo "  -s, --suite SUITE       Run specific test suite (Unit|Integration|All)"
    echo "  -f, --file FILE         Run specific test file"
    echo "  -t, --filter FILTER     Run tests matching filter"
    echo "  -v, --verbose           Run with verbose output"
    echo "  -q, --quiet             Run with minimal output"
    echo "  --stop-on-failure       Stop on first failure"
    echo "  --no-coverage           Run without coverage (default)"
    echo ""
    echo "Examples:"
    echo "  $0                      # Run all tests"
    echo "  $0 -c                   # Run with coverage"
    echo "  $0 -s Unit              # Run unit tests only"
    echo "  $0 -f tests/Unit/Models/ItemTest.php  # Run specific file"
    echo "  $0 -t testItemCreation  # Run specific test method"
    echo "  $0 -v --stop-on-failure # Run with verbose output and stop on failure"
}

# Main execution
main() {
    local coverage=false
    local suite=""
    local file=""
    local filter=""
    local verbose=""
    local quiet=""
    local stop_on_failure=""
    
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            -h|--help)
                show_help
                exit 0
                ;;
            -c|--coverage)
                coverage=true
                shift
                ;;
            -s|--suite)
                suite="$2"
                shift 2
                ;;
            -f|--file)
                file="$2"
                shift 2
                ;;
            -t|--filter)
                filter="$2"
                shift 2
                ;;
            -v|--verbose)
                verbose="--verbose"
                shift
                ;;
            -q|--quiet)
                quiet="--quiet"
                shift
                ;;
            --stop-on-failure)
                stop_on_failure="--stop-on-failure"
                shift
                ;;
            --no-coverage)
                coverage=false
                shift
                ;;
            *)
                print_error "Unknown option: $1"
                show_help
                exit 1
                ;;
        esac
    done
    
    # Check prerequisites
    check_phpunit
    check_config
    
    # Create directories
    create_directories
    
    # Build PHPUnit command
    local phpunit_cmd="./vendor/bin/phpunit --configuration phpunit-api.xml"
    
    if [ "$coverage" = true ]; then
        phpunit_cmd="$phpunit_cmd --coverage-html tests/coverage/html --coverage-text --coverage-clover tests/coverage/coverage.xml"
    fi
    
    if [ -n "$suite" ]; then
        phpunit_cmd="$phpunit_cmd --testsuite \"$suite\""
    fi
    
    if [ -n "$file" ]; then
        phpunit_cmd="$phpunit_cmd \"$file\""
    fi
    
    if [ -n "$filter" ]; then
        phpunit_cmd="$phpunit_cmd --filter \"$filter\""
    fi
    
    if [ -n "$verbose" ]; then
        phpunit_cmd="$phpunit_cmd $verbose"
    fi
    
    if [ -n "$quiet" ]; then
        phpunit_cmd="$phpunit_cmd $quiet"
    fi
    
    if [ -n "$stop_on_failure" ]; then
        phpunit_cmd="$phpunit_cmd $stop_on_failure"
    fi
    
    # Run tests
    print_status "Executing: $phpunit_cmd"
    echo ""
    
    if eval $phpunit_cmd; then
        print_success "All tests passed!"
        
        if [ "$coverage" = true ]; then
            print_success "Coverage report generated in tests/coverage/html/"
        fi
        
        exit 0
    else
        print_error "Some tests failed!"
        exit 1
    fi
}

# Run main function with all arguments
main "$@"
