create # Development Guide

This document provides detailed information for developers working on the Storage Unit Management System.

## 🛠 Required Tools & Software

### Essential Tools

1. **PHP 7.4+**
   - Download from: https://www.php.net/downloads.php
   - macOS: `brew install php`
   - Ubuntu: `sudo apt install php php-mysql php-gd php-mbstring`

2. **MySQL 5.7+**
   - Download from: https://dev.mysql.com/downloads/mysql/
   - macOS: `brew install mysql`
   - Ubuntu: `sudo apt install mysql-server`

3. **Web Server** (Choose one)
   - **Apache**: https://httpd.apache.org/download.cgi
   - **Nginx**: https://nginx.org/en/download.html
   - **PHP Built-in Server**: Included with PHP

### Optional Tools

1. **Docker & Docker Compose**
   - Download from: https://www.docker.com/products/docker-desktop
   - For containerized development

2. **phpMyAdmin**
   - Download from: https://www.phpmyadmin.net/downloads/
   - Web-based MySQL administration
   - Included in Docker setup

3. **VS Code Extensions** (Recommended)
   - PHP Intelephense
   - MySQL
   - HTML CSS Support
   - JavaScript (ES6) code snippets

## 🔧 Development Environment Setup

### Option 1: Traditional Setup

1. **Install Prerequisites**
   ```bash
   # Run the setup script
   ./setup.sh
   ```

2. **Manual Setup Steps**
   - Install PHP with required extensions
   - Install and configure MySQL
   - Import database schema
   - Configure web server
   - Set file permissions

### Option 2: Docker Setup (Recommended)

1. **Install Docker**
   ```bash
   # macOS with Homebrew
   brew install --cask docker

   # Ubuntu
   sudo apt install docker.io docker-compose
   ```

2. **Start Development Environment**
   ```bash
   docker-compose up -d
   ```

3. **Access Applications**
   - Web App: http://localhost:8080
   - phpMyAdmin: http://localhost:8081
   - MySQL: localhost:3306

## 📁 Development Workflow

### Project Structure Understanding

```
storage-unit/
├── index.php              # Main entry point & routing
├── lib/                   # Core application logic
│   ├── db/               # Database layer (MVC Model)
│   ├── guards.php        # Authentication middleware
│   ├── routes.php        # URL routing
│   └── helpers.php       # Utility functions
├── views/                # Presentation layer (MVC View)
├── public/               # Static assets (CSS, JS, images)
└── uploads/              # User uploaded files
```

### Code Organization

1. **Models** (`lib/db/Models/`)
   - `item.php`: Item data model and database operations
   - `user.php`: User authentication and management

2. **Controllers** (`lib/db/Controllers/`)
   - `ItemController.php`: Item business logic

3. **Views** (`views/`)
   - PHP templates for user interface
   - Separated by feature (items, login)

4. **Static Assets** (`public/`)
   - CSS, JavaScript, and images
   - Bootstrap 4 framework
   - Custom styling and functionality

## 🗄️ Database Development

### Schema Information

**Users Table**
- `id`: Primary key
- `email`: Unique email address
- `password`: Hashed password
- `name`: User display name
- `created_at`: Timestamp

**Items Table**
- `id`: Primary key
- `title`: Item name
- `description`: Item details
- `qty`: Quantity
- `user_id`: Foreign key to users
- `img`: Image filename
- `created_at`: Timestamp

### Database Operations

The application uses PDO for all database operations with prepared statements for security:

```php
// Example: Adding an item
$sql = $conexion->prepare("INSERT INTO items (title, description, qty, user_id, img) VALUES (:title, :description, :qty, :user_id , :img)");
$sql->bindParam(':title', $this->title);
// ... bind other parameters
return $sql->execute();
```

## 🔒 Security Considerations

### Implemented Security Measures

1. **SQL Injection Prevention**
   - All queries use PDO prepared statements
   - Parameter binding for user inputs

2. **Password Security**
   - PHP `password_hash()` with BCRYPT
   - Password verification with `password_verify()`

3. **File Upload Security**
   - File type validation (JPEG, PNG only)
   - Unique filename generation to prevent conflicts
   - Upload directory permissions

4. **Session Security**
   - Session-based authentication
   - User-specific data isolation
   - Route protection with guards

### Security Best Practices

1. **Input Validation**
   - Validate all user inputs
   - Sanitize data before database operations
   - Use proper escaping for output

2. **File Handling**
   - Validate file types and sizes
   - Store uploads outside web root when possible
   - Implement proper access controls

## 🧪 Testing

### Manual Testing Checklist

1. **Authentication**
   - [ ] User registration works
   - [ ] Login/logout functionality
   - [ ] Password hashing verification
   - [ ] Session management

2. **Item Management**
   - [ ] Add new items
   - [ ] Edit existing items
   - [ ] Delete items
   - [ ] Image upload functionality
   - [ ] User-specific item isolation

3. **Search Functionality**
   - [ ] Search returns correct results
   - [ ] Case-insensitive search
   - [ ] AJAX search works
   - [ ] Empty search handling

4. **UI/UX**
   - [ ] Responsive design on mobile
   - [ ] Bootstrap components work
   - [ ] JavaScript functionality
   - [ ] Image preview works

### Browser Testing

Test in multiple browsers:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## 🚀 Deployment Considerations

### Production Setup

1. **Server Requirements**
   - PHP 7.4+ with required extensions
   - MySQL 5.7+ or MariaDB
   - Apache/Nginx with mod_rewrite
   - SSL certificate (recommended)

2. **Configuration Changes**
   - Update database credentials
   - Set production PHP settings
   - Configure web server security headers
   - Set up file permissions

3. **Security Hardening**
   - Disable PHP error display
   - Set secure session configuration
   - Implement rate limiting
   - Regular security updates

## 🔄 Common Development Tasks

### Adding New Features

1. **New Database Table**
   - Create migration script
   - Update database.sql
   - Create corresponding model class

2. **New Page/Route**
   - Add route to `lib/routes.php`
   - Create view template
   - Update navigation if needed

3. **New API Endpoint**
   - Create new PHP file
   - Handle request/response
   - Update JavaScript if needed

### Debugging Tips

1. **PHP Errors**
   - Enable error reporting in development
   - Check PHP error logs
   - Use `var_dump()` for debugging

2. **Database Issues**
   - Verify connection parameters
   - Check MySQL error logs
   - Test queries in phpMyAdmin

3. **JavaScript Issues**
   - Check browser console
   - Verify AJAX requests in Network tab
   - Test jQuery functionality

## 📚 Additional Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap 4 Documentation](https://getbootstrap.com/docs/4.6/)
- [jQuery Documentation](https://api.jquery.com/)
- [PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
