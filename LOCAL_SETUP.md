# Local Development Setup (Without Docker)

Since Docker Desktop is having security issues on your Mac, here's how to run the Storage Unit Management System locally.

## 🛠 Prerequisites

You'll need to install:

1. **PHP 7.4+**
2. **MySQL 5.7+**
3. **Web Server** (Apache/Nginx) or use PHP built-in server

## 📦 Installation Steps

### Step 1: Install PHP

```bash
# Install PHP using Homebrew
brew install php

# Install required PHP extensions
brew install php-mysql php-gd php-mbstring

# Verify installation
php --version
```

### Step 2: Install MySQL

```bash
# Install MySQL
brew install mysql

# Start MySQL service
brew services start mysql

# Secure MySQL installation (optional)
mysql_secure_installation
```

### Step 3: Setup Database

```bash
# Connect to MySQL
mysql -u root -p

# Create database and user
CREATE DATABASE storageunit;
CREATE USER 'storageuser'@'localhost' IDENTIFIED BY 'storagepass';
GRANT ALL PRIVILEGES ON storageunit.* TO 'storageuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database schema
mysql -u root -p storageunit < database.sql
```

### Step 4: Configure Database Connection

The database connection is already configured to work locally. Verify in `lib/db/connection.php`:

```php
// Local configuration (should be used when not in Docker)
$user = 'root';
$pass = '';  // Your MySQL root password
$dbname = 'storageunit';
$host = 'localhost';
```

### Step 5: Start Development Server

```bash
# Navigate to project directory
cd /Users/wilbertopachecobatista/Projects/storage-unit

# Start PHP built-in server
php -S localhost:8000

# Or use the setup script
./setup.sh
```

### Step 6: Access Application

- **Web App**: http://localhost:8000
- **Test Account**: admin@example.com / password123

## 🔧 Troubleshooting

### PHP Issues
```bash
# Check PHP extensions
php -m | grep -E "(pdo|mysql|gd|mbstring)"

# If missing extensions, install them
brew install php@7.4
```

### MySQL Issues
```bash
# Check MySQL status
brew services list | grep mysql

# Start MySQL if not running
brew services start mysql

# Check MySQL connection
mysql -u root -p -e "SHOW DATABASES;"
```

### File Permissions
```bash
# Set proper permissions
chmod 755 uploads/
chmod 644 uploads/*
```

## 🎯 Quick Start Commands

```bash
# 1. Install dependencies
brew install php mysql

# 2. Start services
brew services start mysql

# 3. Setup database
mysql -u root -p < database.sql

# 4. Start development server
php -S localhost:8000
```

## 📋 What You'll Have

- ✅ Local PHP development server
- ✅ MySQL database with sample data
- ✅ All application features working
- ✅ No Docker security issues
- ✅ Easy debugging and development

## 🔄 Switching Back to Docker Later

Once you resolve the Docker security issue:

1. Follow the steps in `DOCKER_SECURITY_FIX.md`
2. Run `docker-compose up -d`
3. Access at http://localhost:8080

## 💡 Benefits of Local Setup

- **Faster startup** - No container overhead
- **Direct file access** - Edit files directly
- **Better debugging** - Native PHP error reporting
- **No security warnings** - Uses system PHP/MySQL
- **Easier customization** - Full control over configuration
