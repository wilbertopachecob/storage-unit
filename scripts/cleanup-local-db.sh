#!/bin/bash
# Local Database Cleanup Script
# This script removes any remaining local database traces

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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

echo "🧹 Local Database Cleanup Script"
echo "================================="
echo ""

# Check if running on macOS
if [[ "$OSTYPE" == "darwin"* ]]; then
    print_status "Detected macOS system"
    
    # Stop MySQL service
    print_status "Stopping MySQL service..."
    if brew services list | grep -q "mysql.*started"; then
        brew services stop mysql
        print_success "MySQL service stopped"
    else
        print_warning "MySQL service was not running"
    fi
    
    # Remove local database directories
    print_status "Removing local database directories..."
    if [ -d "/opt/homebrew/var/mysql/storage_unit" ]; then
        rm -rf /opt/homebrew/var/mysql/storage_unit
        print_success "Removed storage_unit database directory"
    fi
    
    if [ -d "/opt/homebrew/var/mysql/storageunit" ]; then
        rm -rf /opt/homebrew/var/mysql/storageunit
        print_success "Removed storageunit database directory"
    fi
    
    if [ -d "/opt/homebrew/var/mysql/storageunit_test" ]; then
        rm -rf /opt/homebrew/var/mysql/storageunit_test
        print_success "Removed storageunit_test database directory"
    fi
    
    # Check for other common MySQL data directories
    for dir in ~/Library/Application\ Support/MySQL/*/data/storage*; do
        if [ -d "$dir" ]; then
            print_status "Removing $dir"
            rm -rf "$dir"
            print_success "Removed $dir"
        fi
    done
    
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    print_status "Detected Linux system"
    
    # Stop MySQL service
    print_status "Stopping MySQL service..."
    if systemctl is-active --quiet mysql; then
        sudo systemctl stop mysql
        print_success "MySQL service stopped"
    else
        print_warning "MySQL service was not running"
    fi
    
    # Remove local database directories (common locations)
    for data_dir in /var/lib/mysql /usr/local/var/mysql; do
        if [ -d "$data_dir" ]; then
            for db in storage_unit storageunit storageunit_test; do
                if [ -d "$data_dir/$db" ]; then
                    print_status "Removing $data_dir/$db"
                    sudo rm -rf "$data_dir/$db"
                    print_success "Removed $data_dir/$db"
                fi
            done
        fi
    done
    
else
    print_warning "Unsupported operating system: $OSTYPE"
    print_status "Please manually remove any local database directories"
fi

# Check for running MySQL processes
print_status "Checking for running MySQL processes..."
if pgrep -f mysql > /dev/null; then
    print_warning "MySQL processes are still running. You may need to kill them manually:"
    pgrep -f mysql | while read pid; do
        echo "  Process $pid: $(ps -p $pid -o comm=)"
    done
    echo "  To kill: sudo kill -9 \$(pgrep -f mysql)"
else
    print_success "No MySQL processes are running"
fi

# Check for local MySQL installations
print_status "Checking for local MySQL installations..."
if command -v mysql > /dev/null; then
    print_warning "MySQL client is still installed locally"
    print_status "Consider uninstalling if you only want to use Docker:"
    if [[ "$OSTYPE" == "darwin"* ]]; then
        echo "  brew uninstall mysql"
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        echo "  sudo apt remove mysql-client mysql-server (Ubuntu/Debian)"
        echo "  sudo yum remove mysql mysql-server (CentOS/RHEL)"
    fi
else
    print_success "No local MySQL installation found"
fi

echo ""
print_success "Local database cleanup completed!"
echo ""
print_status "Next steps:"
echo "1. Use Docker for all database operations:"
echo "   docker-compose up -d"
echo "2. Access the application at: http://localhost:8080"
echo "3. Access phpMyAdmin at: http://localhost:8081"
echo ""
print_status "The application now uses Docker exclusively for database operations."
