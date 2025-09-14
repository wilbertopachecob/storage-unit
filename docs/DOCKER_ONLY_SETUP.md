# Docker-Only Setup Guide

This guide covers the complete Docker-only setup for the Storage Unit Management System.

## 🐳 Why Docker-Only?

- **Consistency**: Same environment for all developers and production
- **Simplicity**: One-command setup and deployment
- **Isolation**: No conflicts with local PHP/MySQL versions
- **Reliability**: Eliminates "works on my machine" issues
- **Scalability**: Easy to scale and deploy

## 📋 Prerequisites

- **Docker Desktop**: [Download here](https://www.docker.com/products/docker-desktop)
- **Git**: [Download here](https://git-scm.com/downloads)

## 🚀 Quick Start

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
   - Web App: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

## 🔧 Configuration

### Environment Variables

The application uses `docker.env` for configuration:

```bash
# Database Configuration
DB_HOST=db
DB_PORT=3306
DB_DATABASE=storageunit
DB_USERNAME=root
DB_PASSWORD=rootpassword

# Application Configuration
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8080

# Google Maps API (Optional)
GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here
```

### Google Maps Setup

1. **Get API Key**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Enable Maps JavaScript API and Places API
   - Create credentials (API Key)

2. **Configure Application**
   ```bash
   # Edit docker.env
   nano docker.env
   
   # Add your API key
   GOOGLE_MAPS_API_KEY=your_actual_api_key_here
   
   # Restart containers
   docker-compose restart
   ```

## 🛠 Development Commands

### Using Docker Helper Script

```bash
# Start development environment
./scripts/docker-dev.sh start

# Stop development environment
./scripts/docker-dev.sh stop

# Restart development environment
./scripts/docker-dev.sh restart

# Run tests
./scripts/docker-dev.sh test

# Run migrations
./scripts/docker-dev.sh migrate

# View logs
./scripts/docker-dev.sh logs

# Access database
./scripts/docker-dev.sh db
```

### Using Composer Scripts

```bash
# Start containers
composer docker:start

# Stop containers
composer docker:stop

# Restart containers
composer docker:restart

# Run tests
composer docker:test

# Run migrations
composer docker:migrate

# View logs
composer docker:logs

# Access database
composer docker:db
```

### Direct Docker Commands

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f

# Access web container
docker-compose exec web bash

# Access database
docker-compose exec db mysql -u root -prootpassword storageunit

# Run PHP commands
docker-compose exec web php scripts/run-migrations.php
```

## 🧪 Testing

### Run Tests

```bash
# All tests
docker-compose exec web composer test

# Specific test file
docker-compose exec web vendor/bin/phpunit tests/Unit/Controllers/ProfileControllerTest.php

# Tests with coverage
docker-compose exec web composer test-coverage
```

### Test Database

The test suite uses a separate test database (`storageunit_test`) that's automatically created and managed.

## 📊 Monitoring

### Container Status

```bash
# Check running containers
docker-compose ps

# View resource usage
docker stats

# Check container health
docker-compose exec web php -v
docker-compose exec db mysql -u root -prootpassword -e "SELECT VERSION();"
```

### Logs

```bash
# All logs
docker-compose logs

# Specific service logs
docker-compose logs web
docker-compose logs db

# Follow logs in real-time
docker-compose logs -f web
```

## 🔧 Troubleshooting

### Common Issues

1. **Port Already in Use**
   ```bash
   # Check what's using the port
   lsof -i :8080
   lsof -i :3307
   
   # Kill the process or change ports in docker-compose.yml
   ```

2. **Database Connection Failed**
   ```bash
   # Check if database is ready
   docker-compose exec db mysql -u root -prootpassword -e "SELECT 1;"
   
   # Restart database
   docker-compose restart db
   ```

3. **Permission Issues**
   ```bash
   # Fix upload directory permissions
   docker-compose exec web chmod -R 755 public/uploads
   ```

4. **Container Won't Start**
   ```bash
   # Check logs
   docker-compose logs web
   
   # Rebuild containers
   docker-compose up -d --build
   ```

### Reset Everything

```bash
# Stop and remove everything
docker-compose down -v

# Remove all images
docker-compose down --rmi all

# Start fresh
docker-compose up -d
./scripts/docker-migrate.sh
```

## 📁 File Structure

```
storage-unit/
├── docker-compose.yml          # Docker services configuration
├── docker.env                  # Environment variables
├── Dockerfile                  # Web container definition
├── scripts/
│   ├── docker-dev.sh          # Development helper script
│   ├── docker-migrate.sh      # Migration runner
│   └── run-migrations.php     # Migration script
└── docs/
    ├── DOCKER_ONLY_SETUP.md   # This file
    ├── QUICKSTART.md          # Quick start guide
    └── DEVELOPMENT.md         # Development guide
```

## 🚀 Production Deployment

For production deployment, update the environment variables in `docker.env`:

```bash
# Production environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Production database (if using external)
DB_HOST=your-production-db-host
DB_USERNAME=your-production-user
DB_PASSWORD=your-production-password
```

## 📚 Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [PHP Docker Images](https://hub.docker.com/_/php)
- [MySQL Docker Images](https://hub.docker.com/_/mysql)
