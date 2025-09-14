# Storage Unit Management System

A modern, secure web-based inventory management application built with PHP.

## 🏗️ Project Structure

```
storage-unit/
├── app/                          # Application core
│   ├── Controllers/              # HTTP Controllers
│   ├── Models/                   # Data Models
│   ├── Services/                 # Business Logic Services
│   ├── Middleware/               # HTTP Middleware
│   ├── Helpers/                  # Helper Functions
│   ├── Validators/               # Input Validation
│   ├── Database/                 # Database Connection
│   └── *.php                     # Application Entry Points
├── config/                       # Configuration Files
│   ├── app/                      # Application Config
│   └── database/                 # Database Config
├── public/                       # Web Root (Document Root)
│   ├── css/                      # Stylesheets
│   ├── js/                       # JavaScript Files
│   ├── img/                      # Images
│   ├── uploads/                  # User Uploads
│   └── .htaccess                 # Apache Configuration
├── resources/                    # Non-PHP Resources
│   └── views/                    # View Templates
├── routes/                       # Route Definitions
├── scripts/                      # Shell Scripts
├── storage/                      # Storage Directories
│   ├── logs/                     # Log Files
│   └── framework/                # Framework Cache
├── tests/                        # Test Suites
│   ├── Unit/                     # Unit Tests
│   ├── Feature/                  # Feature Tests
│   └── Integration/              # Integration Tests
├── vendor/                       # Composer Dependencies
├── .htaccess                     # Root Apache Configuration
├── composer.json                 # Composer Configuration
├── docker-compose.yml            # Docker Configuration
├── Dockerfile                    # Docker Image Definition
└── index.php                     # Application Entry Point
```

## 🚀 Features

- **Modern Architecture**: PSR-4 autoloading, MVC pattern
- **Security**: CSRF protection, input validation, secure file uploads
- **Database**: PDO-based database abstraction
- **Testing**: PHPUnit test suite with unit, feature, and integration tests
- **Docker Support**: Complete Docker development environment
- **Responsive Design**: Bootstrap-based responsive UI

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- Composer for dependency management

## 🛠️ Installation

### Using Docker (Recommended)

1. Clone the repository
2. Run `docker-compose up -d`
3. Access the application at `http://localhost`

### Manual Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Configure database in `config/app/config.php`
4. Import database schema: `mysql -u root -p storage_unit < config/database/database.sql`
5. Set up web server to point to the `public/` directory
6. Ensure `public/uploads/` is writable

## 🧪 Testing

Run the test suite:
```bash
composer test
```

Run tests with coverage:
```bash
composer test-coverage
```

## 🔧 Configuration

All configuration is managed through files in the `config/` directory:

- `config/app/config.php` - Application settings
- `config/database/` - Database configuration and schema

## 📁 Directory Structure Details

### `/app/`
Contains the core application logic:
- **Controllers/**: Handle HTTP requests and responses
- **Models/**: Represent data and business logic
- **Services/**: Complex business operations
- **Middleware/**: Request/response processing
- **Helpers/**: Utility functions
- **Validators/**: Input validation logic
- **Database/**: Database connection and queries

### `/public/`
The web-accessible directory:
- **css/**: Stylesheets
- **js/**: JavaScript files
- **img/**: Static images
- **uploads/**: User-uploaded files

### `/resources/views/`
View templates organized by feature:
- **items/**: Item management views
- **login/**: Authentication views
- **header.php**: Common header
- **footer.php**: Common footer

### `/routes/`
Route definitions and URL mapping

### `/scripts/`
Shell scripts for deployment and maintenance

### `/storage/`
Application storage:
- **logs/**: Application logs
- **framework/**: Framework cache

## 🔒 Security Features

- CSRF token protection
- Input sanitization and validation
- Secure file upload handling
- SQL injection prevention
- XSS protection headers
- Directory traversal protection

## 🐳 Docker Development

The project includes complete Docker support:

- **Dockerfile**: PHP 8.1 with Apache
- **docker-compose.yml**: Multi-container setup
- **docker/**: Additional Docker configuration

## 📝 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**Engr. Wilberto Pacheco Batista**
- Email: admin@example.com

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📞 Support

For support and questions, please open an issue on GitHub.