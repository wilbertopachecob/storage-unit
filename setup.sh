#!/bin/bash

# Storage Unit Management System Setup Script
# This script helps set up the local development environment

echo "🚀 Setting up Storage Unit Management System..."

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 7.4 or higher."
    echo "   - macOS: brew install php"
    echo "   - Ubuntu: sudo apt install php php-mysql"
    echo "   - Windows: Download from https://www.php.net/downloads.php"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✅ PHP version: $PHP_VERSION"

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL is not installed. Please install MySQL 5.7 or higher."
    echo "   - macOS: brew install mysql"
    echo "   - Ubuntu: sudo apt install mysql-server"
    echo "   - Windows: Download from https://dev.mysql.com/downloads/mysql/"
    exit 1
fi

echo "✅ MySQL is installed"

# Check if required PHP extensions are installed
echo "🔍 Checking PHP extensions..."

REQUIRED_EXTENSIONS=("pdo" "pdo_mysql" "gd" "mbstring" "fileinfo")
MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -q "$ext"; then
        MISSING_EXTENSIONS+=("$ext")
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -ne 0 ]; then
    echo "❌ Missing PHP extensions: ${MISSING_EXTENSIONS[*]}"
    echo "   Please install the missing extensions:"
    echo "   - macOS: brew install php@7.4"
    echo "   - Ubuntu: sudo apt install php-mysql php-gd php-mbstring"
    exit 1
fi

echo "✅ All required PHP extensions are installed"

# Set up database
echo "🗄️  Setting up database..."

read -p "Enter MySQL root password (press Enter if no password): " MYSQL_PASSWORD

if [ -z "$MYSQL_PASSWORD" ]; then
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS storageunit;"
    mysql -u root storageunit < database.sql
else
    mysql -u root -p"$MYSQL_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS storageunit;"
    mysql -u root -p"$MYSQL_PASSWORD" storageunit < database.sql
fi

echo "✅ Database setup complete"

# Set up file permissions
echo "🔐 Setting up file permissions..."
chmod 755 uploads/
echo "✅ File permissions set"

# Create .htaccess for Apache (optional)
if [ ! -f .htaccess ]; then
    cat > .htaccess << EOF
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
EOF
    echo "✅ .htaccess file created"
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Update database credentials in lib/db/connection.php if needed"
echo "2. Start your web server:"
echo "   - Using PHP built-in server: php -S localhost:8000"
echo "   - Using Apache/Nginx: Configure virtual host"
echo "   - Using Docker: docker-compose up"
echo ""
echo "3. Access the application at:"
echo "   - http://localhost:8000 (PHP built-in server)"
echo "   - http://localhost:8080 (Docker setup)"
echo ""
echo "4. Default test accounts:"
echo "   - admin@example.com / password123"
echo "   - test@example.com / password123"
echo ""
echo "🔧 For Docker setup, run: docker-compose up -d"
