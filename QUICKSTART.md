# Quick Start Guide

Get the Storage Unit Management System running in 5 minutes!

## 🚀 Option 1: Docker (Easiest)

1. **Prerequisites**
   - Install [Docker Desktop](https://www.docker.com/products/docker-desktop)

2. **Start the Application**
   ```bash
   docker-compose up -d
   ```

3. **Access the Application**
   - Web App: http://localhost:8080
   - Database Admin: http://localhost:8081 (phpMyAdmin)

4. **Test Login**
   - Email: `admin@example.com`
   - Password: `password123`

## 🛠 Option 2: Local Development

1. **Prerequisites**
   - PHP 7.4+
   - MySQL 5.7+
   - Web server (Apache/Nginx) or PHP built-in server

2. **Quick Setup**
   ```bash
   # Run the setup script
   ./setup.sh
   ```

3. **Start PHP Built-in Server**
   ```bash
   php -S localhost:8000
   ```

4. **Access the Application**
   - Web App: http://localhost:8000

## 📋 What You'll See

- **Home Page**: Welcome screen with navigation
- **Sign Up/Login**: User authentication
- **Items List**: Grid view of your storage items
- **Add Item**: Form to add new items with image upload
- **Search**: Real-time search functionality
- **Edit/Delete**: Item management actions

## 🎯 Key Features to Test

1. **Create Account** → Register with email/password
2. **Add Items** → Upload images and set quantities
3. **Search Items** → Use the search bar to find items
4. **Edit Items** → Modify item details and images
5. **Delete Items** → Remove items from inventory

## 🔧 Troubleshooting

### Docker Issues
```bash
# Check if containers are running
docker-compose ps

# View logs
docker-compose logs

# Restart services
docker-compose restart
```

### Local Setup Issues
```bash
# Check PHP version
php -v

# Check MySQL status
mysql --version

# Check database connection
mysql -u root -p -e "SHOW DATABASES;"
```

### Common Problems

1. **"Database connection failed"**
   - Check MySQL is running
   - Verify credentials in `lib/db/connection.php`

2. **"Permission denied" on uploads**
   ```bash
   chmod 755 uploads/
   ```

3. **"Page not found" errors**
   - Check web server configuration
   - Ensure mod_rewrite is enabled (Apache)

## 📱 Mobile Testing

The application is responsive! Test on mobile devices:
- Open browser developer tools
- Toggle device simulation
- Test touch interactions

## 🎨 Customization

### Styling
- Edit `public/css/style.css` for custom styles
- Modify Bootstrap classes in HTML templates

### Functionality
- Add new routes in `lib/routes.php`
- Create new views in `views/` directory
- Extend models in `lib/db/Models/`

## 📞 Need Help?

1. Check the full [README.md](README.md) for detailed setup
2. Review [DEVELOPMENT.md](DEVELOPMENT.md) for development info
3. Create an issue in the repository

---

**Happy coding! 🎉**
