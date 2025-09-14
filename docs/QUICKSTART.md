# Quick Start Guide

Get the Storage Unit Management System running in 5 minutes!

## 🚀 Docker Setup (Only Method Supported)

1. **Prerequisites**
   - Install [Docker Desktop](https://www.docker.com/products/docker-desktop)

2. **Start the Application**
   ```bash
   docker-compose up -d
   ```

3. **Run Database Migrations**
   ```bash
   ./scripts/docker-migrate.sh
   ```

4. **Access the Application**
   - Web App: http://localhost:8080
   - Database Admin: http://localhost:8081 (phpMyAdmin)

5. **Test Login**
   - Email: `admin@example.com`
   - Password: `password123`

## 🔧 Configuration

1. **Google Maps Setup (Optional)**
   ```bash
   # Edit docker.env file
   nano docker.env
   # Add your Google Maps API key
   GOOGLE_MAPS_API_KEY=your_actual_api_key_here
   
   # Restart containers
   docker-compose restart
   ```

## 📋 What You'll See

- **Home Page**: Welcome screen with navigation
- **Sign Up/Login**: User authentication
- **Profile Page**: User profile with storage unit management
- **Items List**: Grid view of your storage items
- **Add Item**: Form to add new items with image upload
- **Search**: Real-time search functionality
- **Edit/Delete**: Item management actions

## 🎯 Key Features to Test

1. **Create Account** → Register with email/password
2. **Set Storage Unit** → Configure your storage location with Google Maps
3. **Upload Profile Picture** → Add a profile picture
4. **Add Items** → Upload images and set quantities
5. **Search Items** → Use the search bar to find items
6. **Edit Items** → Modify item details and images
7. **Delete Items** → Remove items from inventory

## 🔧 Troubleshooting

### Docker Issues
```bash
# Check if containers are running
docker-compose ps

# View logs
docker-compose logs

# Restart services
docker-compose restart

# Rebuild containers
docker-compose up -d --build
```

### Database Issues
```bash
# Check database connection
docker-compose exec web php -r "echo 'Testing database connection...\n'; try { \$pdo = new PDO('mysql:host=db;dbname=storageunit', 'root', 'rootpassword'); echo 'Database connected successfully!\n'; } catch (Exception \$e) { echo 'Database connection failed: ' . \$e->getMessage() . '\n'; }"

# Reset database
docker-compose down -v
docker-compose up -d
./scripts/docker-migrate.sh
```

### Migration Issues
```bash
# Run migrations manually
docker-compose exec web php scripts/run-migrations.php

# Check migration status
docker-compose exec db mysql -u root -prootpassword -e "DESCRIBE storageunit.users;"
```

## 🧪 Testing

```bash
# Run all tests
docker-compose exec web composer test

# Run specific test
docker-compose exec web vendor/bin/phpunit tests/Unit/Controllers/ProfileControllerTest.php
```

## 📚 Next Steps

- Read the [Development Guide](DEVELOPMENT.md) for development setup
- Check [Docker Setup Complete](DOCKER_SETUP_COMPLETE.md) for detailed Docker configuration
- Review [Debug Guide](DEBUG.md) for troubleshooting