# Storage Unit Management System

A web-based inventory management application built with PHP that allows users to manage their storage items with an intuitive interface.

## 🎯 Project Overview

The Storage Unit Management System is a personal inventory management application that enables users to:

- **User Authentication**: Sign up and sign in with secure password hashing
- **Item Management**: Add, edit, view, and delete storage items
- **Image Upload**: Upload and manage item images with preview functionality
- **Search Functionality**: Search through items by title with real-time results
- **Responsive Design**: Mobile-friendly interface using Bootstrap 4
- **User-Specific Data**: Each user can only access their own items

## 🛠 Technology Stack

### Backend
- **PHP 8.4** - Latest server-side scripting language
- **MySQL 8.0** - Modern database management system
- **PDO** - Database abstraction layer for secure database operations

### Frontend
- **HTML5** - Markup language
- **CSS3** - Styling with custom styles
- **Bootstrap 4.2.1** - CSS framework for responsive design
- **JavaScript (ES5)** - Client-side functionality
- **jQuery 3.3.1** - JavaScript library for DOM manipulation
- **Font Awesome 5.6.3** - Icon library

### External Dependencies
- Google Fonts (Rancho font family)
- Bootstrap CDN
- jQuery CDN
- Font Awesome CDN

## 📋 Prerequisites

Before setting up the project, ensure you have the following installed:

1. **Web Server**: Apache or Nginx
2. **PHP**: Version 7.4 or higher
3. **MySQL**: Version 5.7 or higher
4. **PHP Extensions**:
   - PDO
   - PDO_MySQL
   - GD (for image processing)
   - mbstring
   - fileinfo

## 🚀 Installation & Setup

### Option 1: Docker Setup (Recommended)

1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd storage-unit
   ```

2. **Start with Docker**:
   ```bash
   # Quick start (recommended)
   ./start.sh
   
   # Or manually
   docker-compose up -d
   ```

3. **Access the Application**:
   - **Web App**: http://localhost:8080
   - **phpMyAdmin**: http://localhost:8081

### 🔐 Test Credentials

The application comes with pre-configured test accounts for both environments:

| Environment | Email | Password | Notes |
|-------------|-------|----------|-------|
| **Local** | admin@example.com | password123 | For local development |
| **Docker** | admin@example.com | password | For Docker environment |
| **Docker** | test@example.com | password | Additional test user |

> **Note**: The Docker and local environments use different databases with different password hashes. Make sure to use the correct credentials for your environment.

### Option 2: Local Development Setup

1. **Prerequisites**:
   ```bash
   # Install PHP and MySQL
   brew install php mysql
   
   # Start MySQL
   brew services start mysql
   ```

2. **Database Setup**:
   ```bash
   # Create database and import schema
   mysql -u root -e "CREATE DATABASE storageunit;"
   mysql -u root storageunit < database.sql
   ```

3. **Start Development Server**:
   ```bash
   php -S localhost:8000
   ```

### 4. Web Server Configuration

#### Apache Configuration
1. Ensure `mod_rewrite` is enabled
2. Set document root to the project directory
3. Configure virtual host if needed

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/storage-unit;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. File Permissions

Set appropriate permissions for the uploads directory:

```bash
chmod 755 uploads/
chmod 644 uploads/*
```

### 6. PHP Configuration

Ensure your `php.ini` has the following settings:

```ini
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

## 🔧 Local Development Setup

### Option 1: Using XAMPP/WAMP/MAMP

1. Install XAMPP, WAMP, or MAMP
2. Place the project in the web server directory (e.g., `htdocs/` for XAMPP)
3. Start Apache and MySQL services
4. Access the application at `http://localhost/storage-unit`

### Option 2: Using PHP Built-in Server

1. Navigate to the project directory
2. Start the PHP development server:
   ```bash
   php -S localhost:8000
   ```
3. Access the application at `http://localhost:8000`

### Option 3: Using Docker

Create a `docker-compose.yml` file:

```yaml
version: '3.8'
services:
  web:
    image: php:7.4-apache
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - APACHE_DOCUMENT_ROOT=/var/www/html

  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: storageunit
      MYSQL_USER: storageuser
      MYSQL_PASSWORD: storagepass
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

Run with:
```bash
docker-compose up -d
```

## 📁 Project Structure

```
storage-unit/
├── index.php                 # Main entry point
├── home.php                  # Home page content
├── search.php                # Search results page
├── searchScript.php          # AJAX search endpoint
├── lib/                      # Core application logic
│   ├── db/
│   │   ├── connection.php    # Database connection
│   │   ├── Controllers/
│   │   │   └── ItemController.php
│   │   └── Models/
│   │       ├── item.php      # Item model
│   │       └── user.php      # User model
│   ├── guards.php            # Authentication guards
│   ├── helpers.php           # Utility functions
│   ├── routes.php            # URL routing
│   ├── signsHandlers.php     # Authentication handlers
│   ├── user.php              # User utilities
│   └── validator.php         # Input validation
├── views/                    # View templates
│   ├── items/
│   │   ├── addItem.php       # Add item form
│   │   ├── editItem.php      # Edit item form
│   │   └── itemsList.php     # Items listing
│   └── login/
│       ├── signIn.php        # Login form
│       └── signUp.php        # Registration form
├── partials/                 # Reusable components
│   ├── header.php            # Site header
│   └── footer.php            # Site footer
├── public/                   # Static assets
│   ├── css/
│   │   └── style.css         # Custom styles
│   ├── js/
│   │   ├── card.js           # Card interactions
│   │   ├── main.js           # Main JavaScript
│   │   ├── searchLoader.js   # Search functionality
│   │   └── upload-image-preview.js
│   └── img/
│       └── storage-unit.jpg  # Background image
└── uploads/                  # User uploaded files
```

## 🔑 Key Features

### Authentication System
- Secure user registration with email validation
- Password hashing using PHP's `password_hash()`
- Session-based authentication
- Protected routes with guards

### Item Management
- CRUD operations for storage items
- Image upload with validation (JPEG, PNG)
- Quantity tracking
- User-specific item isolation

### Search Functionality
- Real-time search using AJAX
- Case-insensitive title search
- Responsive search results

### Security Features
- SQL injection prevention using PDO prepared statements
- XSS protection with proper output escaping
- File upload validation
- Session security

## 🚦 Usage

1. **Register**: Create a new account
2. **Login**: Sign in with your credentials
3. **Add Items**: Use the "Add Item" form to store new items
4. **View Items**: Browse your inventory in card or list view
5. **Edit Items**: Update item details and images
6. **Search**: Find items quickly using the search functionality
7. **Delete Items**: Remove items you no longer need

## 🧪 Testing

The application includes a comprehensive test suite using PHPUnit for unit, integration, and feature testing.

### Prerequisites for Testing

1. **PHPUnit**: Install via Composer
2. **Test Database**: Create a separate test database
3. **PHP Extensions**: Ensure required extensions are installed

### Test Setup

1. **Install Dependencies**:
   ```bash
   composer install
   ```

2. **Create Test Database**:
   ```sql
   CREATE DATABASE storageunit_test;
   ```

3. **Import Test Schema**:
   ```bash
   mysql -u root -p storageunit_test < tests/database_test.sql
   ```

4. **Configure Test Environment**:
   Update the database credentials in `phpunit.xml` if needed:
   ```xml
   <env name="DB_HOST" value="localhost"/>
   <env name="DB_NAME" value="storageunit_test"/>
   <env name="DB_USER" value="root"/>
   <env name="DB_PASS" value=""/>
   ```

### Running Tests

1. **Run All Tests**:
   ```bash
   composer test
   # or
   ./vendor/bin/phpunit
   ```

2. **Run Specific Test Suites**:
   ```bash
   # Unit tests only
   ./vendor/bin/phpunit --testsuite Unit
   
   # Integration tests only
   ./vendor/bin/phpunit --testsuite Integration
   
   # Feature tests only
   ./vendor/bin/phpunit --testsuite Feature
   ```

3. **Run Specific Test Classes**:
   ```bash
   # Test User model
   ./vendor/bin/phpunit tests/Unit/Models/UserTest.php
   
   # Test Security class
   ./vendor/bin/phpunit tests/Unit/Core/SecurityTest.php
   
   # Test Item model
   ./vendor/bin/phpunit tests/Unit/Models/ItemTest.php
   ```

4. **Run Tests with Coverage**:
   ```bash
   composer test-coverage
   # or
   ./vendor/bin/phpunit --coverage-html coverage
   ```

5. **Run Tests with Verbose Output**:
   ```bash
   ./vendor/bin/phpunit --verbose
   ```

### Test Structure

```
tests/
├── TestCase.php                 # Base test class
├── Unit/                       # Unit tests
│   ├── Core/
│   │   └── SecurityTest.php    # Security class tests
│   └── Models/
│       ├── UserTest.php        # User model tests
│       └── ItemTest.php        # Item model tests
├── Integration/                # Integration tests
│   ├── AuthControllerTest.php  # Authentication flow tests
│   └── ItemControllerTest.php  # Item management tests
├── Feature/                    # Feature tests
│   ├── UserRegistrationTest.php
│   ├── ItemManagementTest.php
│   └── SearchFunctionalityTest.php
└── database_test.sql          # Test database schema
```

### Test Categories

1. **Unit Tests** (`tests/Unit/`):
   - Test individual classes and methods in isolation
   - Mock dependencies where necessary
   - Focus on business logic and data validation

2. **Integration Tests** (`tests/Integration/`):
   - Test interaction between different components
   - Test database operations with real database
   - Test controller methods with proper request/response flow

3. **Feature Tests** (`tests/Feature/`):
   - Test complete user workflows
   - Test end-to-end functionality
   - Test user interface interactions

### Writing Tests

1. **Test Naming Convention**:
   - Test methods should start with `test` or use `@test` annotation
   - Use descriptive names: `testUserCanRegisterWithValidData()`

2. **Test Structure** (AAA Pattern):
   ```php
   public function testUserRegistration()
   {
       // Arrange - Set up test data
       $email = 'test@example.com';
       $password = 'password'; // Use 'password123' for local, 'password' for Docker
       $name = 'Test User';
       
       // Act - Execute the code being tested
       $user = new User($email, $password, $name);
       $result = $user->create();
       
       // Assert - Verify the results
       $this->assertTrue($result);
       $this->assertNotNull($user->getId());
   }
   ```

3. **Database Testing**:
   - Use the `TestCase` base class for database tests
   - Database is reset before each test
   - Use `createTestUser()` and `createTestItem()` helper methods

### Test Configuration

The test configuration is defined in `phpunit.xml`:

- **Bootstrap**: `vendor/autoload.php`
- **Test Suites**: Unit, Integration, Feature
- **Coverage**: HTML and text reports
- **Environment**: Testing environment variables
- **Database**: Separate test database

### Continuous Integration

For CI/CD pipelines, use:

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Run tests
composer test

# Generate coverage report
composer test-coverage
```

### Troubleshooting Tests

1. **Database Connection Issues**:
   - Verify test database exists and is accessible
   - Check database credentials in `phpunit.xml`
   - Ensure MySQL service is running

2. **Permission Issues**:
   - Ensure test database user has proper permissions
   - Check file permissions for coverage reports

3. **Memory Issues**:
   - Increase PHP memory limit: `php -d memory_limit=512M ./vendor/bin/phpunit`
   - Use `--process-isolation` flag for large test suites

4. **Test Failures**:
   - Run tests with `--verbose` flag for detailed output
   - Check test database state between runs
   - Verify all dependencies are installed

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Verify MySQL service is running
   - Check database credentials in `connection.php`
   - Ensure database exists

2. **File Upload Issues**:
   - Check PHP upload limits in `php.ini`
   - Verify `uploads/` directory permissions
   - Ensure sufficient disk space

3. **Session Issues**:
   - Check PHP session configuration
   - Verify session storage directory permissions

4. **Path Issues**:
   - Update base paths in `index.php` if not using root directory
   - Check web server document root configuration

## 📝 Development Notes

- The application uses a simple MVC-like structure
- Database operations are handled through PDO for security
- Frontend uses Bootstrap for responsive design
- JavaScript handles dynamic interactions and AJAX requests
- Image uploads are stored in the `uploads/` directory

## 🔄 Future Enhancements

- [ ] Category management for items
- [ ] Advanced search filters
- [ ] Data export functionality
- [ ] Item sharing between users
- [ ] Mobile app development
- [ ] API endpoints for external integrations

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

If you encounter any issues or have questions, please create an issue in the repository.
