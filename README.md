# Storage Unit Management System

A modern, secure web-based inventory management application built with PHP.

**Version**: 2.0.0  
**PHP Version**: >=7.4  
**License**: MIT

## 🏗️ Project Structure

```
storage-unit/
├── app/                          # Application core
│   ├── Controllers/              # HTTP Controllers
│   │   ├── AuthController.php    # User authentication
│   │   ├── ItemController.php    # Basic item management
│   │   ├── EnhancedItemController.php # Advanced item operations
│   │   ├── CategoryController.php # Category management
│   │   ├── LocationController.php # Location management
│   │   ├── ProfileController.php # User profile management
│   │   └── ExportController.php  # Data export functionality
│   ├── Models/                   # Data Models
│   │   ├── User.php              # User model
│   │   ├── Item.php              # Item model
│   │   ├── Category.php          # Category model
│   │   └── Location.php          # Location model
│   ├── Core/                     # Core functionality
│   │   ├── Database.php          # Database connection
│   │   ├── Security.php          # Security utilities
│   │   ├── LoggerInterface.php   # Logging interface
│   │   ├── LoggerFactory.php     # Logger factory
│   │   └── FileLogger.php        # File-based logging
│   ├── Helpers/                  # Helper Functions
│   │   ├── helpers.php           # General helpers
│   │   ├── ImageProcessor.php    # Image processing
│   │   └── ImageUploader.php     # Image upload handling
│   ├── Validators/               # Input Validation
│   │   └── Validator.php         # Validation utilities
│   ├── Database/                 # Database Connection
│   │   ├── Connection.php        # Database connection
│   │   ├── Migrations/           # Database migrations
│   │   └── Seeders/              # Database seeders
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
├── docs/                         # Documentation Files
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

- **Modern Architecture**: PSR-4 autoloading, MVC pattern, dependency injection
- **User Management**: Secure authentication, registration, and profile management
- **Item Management**: Full CRUD operations for storage items with image uploads
- **Categories & Tags**: Organize items with custom categories and color coding
- **Location Tracking**: Hierarchical location management (Building → Room → Shelf → Box)
- **Advanced Search**: Multi-criteria search with category and location filtering
- **Export Functionality**: CSV export for items, categories, and locations
- **Analytics & Reporting**: Item counts, storage utilization, and statistics
- **Security**: CSRF protection, input validation, secure file uploads, SQL injection prevention
- **Database**: PDO-based database abstraction with migrations
- **Testing**: PHPUnit test suite with unit, feature, and integration tests
- **Docker Support**: Complete Docker development environment
- **Responsive Design**: Bootstrap-based responsive UI with mobile optimization

## 📋 Requirements

- Docker and Docker Compose
- Git

## 🛠️ Installation

### Docker Setup (Only Method Supported)

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/storage-unit.git
   cd storage-unit
   ```

2. **Start the application**
   ```bash
   docker-compose up -d
   ```

3. **Run database migrations**
   ```bash
   ./scripts/docker-migrate.sh
   ```

4. **Access the application**
   - Web Application: http://localhost:8080
   - phpMyAdmin: http://localhost:8081
   - Default admin: admin@example.com / password123

## 🎯 What You Can Do

### User Management
- **Register & Login**: Secure user authentication with password hashing
- **Profile Management**: Update personal information and profile pictures
- **Storage Unit Setup**: Configure your storage location with Google Maps integration

### Item Management
- **Add Items**: Upload items with images, descriptions, and quantities
- **Organize by Categories**: Create custom categories with colors and icons
- **Track Locations**: Organize items in hierarchical locations (Building → Room → Shelf → Box)
- **Search & Filter**: Advanced search with category and location filtering
- **Edit & Delete**: Full CRUD operations for all items

### Analytics & Export
- **View Statistics**: See total items, quantities, and distribution
- **Export Data**: Download CSV files of your inventory
- **Category Reports**: Analyze items by category
- **Location Reports**: Track storage utilization by location

### Advanced Features
- **Image Processing**: Automatic image optimization and resizing
- **Real-time Search**: AJAX-powered search with instant results
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Secure Uploads**: Protected file upload with validation

5. **Configure Google Maps (Optional)**
   ```bash
   # Edit docker.env file
   nano docker.env
   # Add your Google Maps API key
   GOOGLE_MAPS_API_KEY=your_actual_api_key_here
   
   # Restart containers
   docker-compose restart
   ```

### Docker Services

- **Web Server**: Apache with PHP 8.1 on port 8080
- **Database**: MySQL 8.0 on port 3307 (internal: 3306)
- **phpMyAdmin**: Database management on port 8081

## 📚 Documentation

All detailed documentation is organized in the `docs/` folder:

- **[docs/INDEX.md](docs/INDEX.md)** - Documentation index and navigation
- **[docs/QUICKSTART.md](docs/QUICKSTART.md)** - Quick setup guide (5-minute setup)
- **[docs/DOCKER_ONLY_SETUP.md](docs/DOCKER_ONLY_SETUP.md)** - Complete Docker-only setup guide
- **[docs/DOCKER_SETUP_COMPLETE.md](docs/DOCKER_SETUP_COMPLETE.md)** - Docker setup and configuration guide
- **[docs/DEVELOPMENT.md](docs/DEVELOPMENT.md)** - Development guidelines and best practices
- **[docs/FILESYSTEM_IMPROVEMENTS.md](docs/FILESYSTEM_IMPROVEMENTS.md)** - Project structure and organization improvements
- **[docs/DEBUG.md](docs/DEBUG.md)** - Debugging, logging, and troubleshooting guide
- **[docs/GOOGLE_MAPS_SETUP.md](docs/GOOGLE_MAPS_SETUP.md)** - Google Maps integration setup

## 🧪 Testing

Run the test suite in Docker:
```bash
# Run all tests
docker-compose exec web composer test

# Run tests with coverage
docker-compose exec web composer test-coverage

# Run specific test file
docker-compose exec web vendor/bin/phpunit tests/Unit/Controllers/ProfileControllerTest.php
```

## 🔧 Configuration

All configuration is managed through files in the `config/` directory:

- `config/app/config.php` - Application settings
- `config/database/` - Database configuration and schema

## 📦 Dependencies

The project uses Composer for dependency management:

- **PHP**: >=7.4
- **PHPUnit**: ^10.0 (for testing)
- **PSR-4 Autoloading**: Namespace `StorageUnit\`

### Available Composer Scripts

```bash
# Testing
composer test                    # Run PHPUnit tests
composer test-coverage          # Run tests with coverage report

# Docker Commands
composer docker:start           # Start Docker containers
composer docker:stop            # Stop Docker containers
composer docker:restart         # Restart Docker containers
composer docker:test            # Run tests in Docker
composer docker:migrate         # Run database migrations
composer docker:logs            # View Docker logs
composer docker:db              # Access database shell
```

## 📁 Directory Structure Details

### `/app/`
Contains the core application logic:
- **Controllers/**: Handle HTTP requests and responses
  - `AuthController`: User authentication and registration
  - `ItemController`: Basic item CRUD operations
  - `EnhancedItemController`: Advanced item management with search and analytics
  - `CategoryController`: Category management and organization
  - `LocationController`: Location hierarchy and management
  - `ProfileController`: User profile and storage unit configuration
  - `ExportController`: Data export functionality (CSV)
- **Models/**: Represent data and business logic
  - `User`: User authentication, profile, and storage unit management
  - `Item`: Storage items with categories, locations, and metadata
  - `Category`: Item categorization with colors and icons
  - `Location`: Hierarchical location management (Building → Room → Shelf → Box)
- **Core/**: Core system functionality
  - `Database`: PDO-based database connection and management
  - `Security`: CSRF protection, input validation, password hashing
  - `LoggerInterface`, `LoggerFactory`, `FileLogger`: Comprehensive logging system
- **Helpers/**: Utility functions
  - `ImageProcessor`: Image optimization and processing
  - `ImageUploader`: Secure file upload handling
- **Validators/**: Input validation logic
- **Database/**: Database migrations and seeders

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

## 📊 Analytics & Reporting

The application includes comprehensive analytics and reporting features:

- **Item Statistics**: Total item count, quantity tracking, and user-specific metrics
- **Category Analytics**: Item distribution by category with counts and percentages
- **Location Utilization**: Storage space usage and location-based item distribution
- **Export Capabilities**: CSV export for items, categories, and locations
- **Search Analytics**: Track search queries and filter usage
- **User Activity**: Monitor user engagement and system usage patterns

### Export Options

- **Full Inventory Export**: Complete item list with categories and locations
- **Category-based Export**: Items filtered by specific categories
- **Location-based Export**: Items filtered by storage locations
- **Search Results Export**: Export filtered search results
- **Metadata Export**: Categories and locations with item counts

## 🔒 Security Features

- CSRF token protection
- Input sanitization and validation
- Secure file upload handling
- SQL injection prevention
- XSS protection headers
- Directory traversal protection
- Password hashing with bcrypt
- Session management and authentication

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