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
   - **Test Account**: admin@example.com / password123

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
