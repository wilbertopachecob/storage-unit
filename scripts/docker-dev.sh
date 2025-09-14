#!/bin/bash
# Docker Development Helper Script

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

# Function to check if Docker is running
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        print_error "Docker is not running. Please start Docker first."
        exit 1
    fi
}

# Function to start development environment
start_dev() {
    print_status "Starting development environment..."
    docker-compose up -d
    print_success "Containers started successfully!"
    
    print_status "Waiting for database to be ready..."
    sleep 10
    
    print_status "Running database migrations..."
    docker-compose exec web php scripts/run-migrations.php
    print_success "Migrations completed!"
    
    print_status "Development environment is ready!"
    echo -e "Web App: ${GREEN}http://localhost:8080${NC}"
    echo -e "phpMyAdmin: ${GREEN}http://localhost:8081${NC}"
}

# Function to stop development environment
stop_dev() {
    print_status "Stopping development environment..."
    docker-compose down
    print_success "Development environment stopped!"
}

# Function to restart development environment
restart_dev() {
    print_status "Restarting development environment..."
    docker-compose restart
    print_success "Development environment restarted!"
}

# Function to run tests
run_tests() {
    print_status "Running tests..."
    docker-compose exec web composer test
    print_success "Tests completed!"
}

# Function to run migrations
run_migrations() {
    print_status "Running database migrations..."
    docker-compose exec web php scripts/run-migrations.php
    print_success "Migrations completed!"
}

# Function to view logs
view_logs() {
    print_status "Viewing container logs..."
    docker-compose logs -f
}

# Function to access database
access_db() {
    print_status "Accessing database..."
    docker-compose exec db mysql -u root -prootpassword storageunit
}

# Function to show help
show_help() {
    echo "Docker Development Helper Script"
    echo ""
    echo "Usage: $0 [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  start       Start development environment"
    echo "  stop        Stop development environment"
    echo "  restart     Restart development environment"
    echo "  test        Run tests"
    echo "  migrate     Run database migrations"
    echo "  logs        View container logs"
    echo "  db          Access database"
    echo "  help        Show this help message"
    echo ""
}

# Main script logic
case "${1:-help}" in
    start)
        check_docker
        start_dev
        ;;
    stop)
        stop_dev
        ;;
    restart)
        check_docker
        restart_dev
        ;;
    test)
        check_docker
        run_tests
        ;;
    migrate)
        check_docker
        run_migrations
        ;;
    logs)
        view_logs
        ;;
    db)
        check_docker
        access_db
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        print_error "Unknown command: $1"
        show_help
        exit 1
        ;;
esac
