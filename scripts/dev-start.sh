#!/bin/bash

# Development Docker Setup Script
# This script sets up Docker for development with live code changes

echo "🚀 Starting Storage Unit Development Environment..."

# Stop any existing containers
echo "📦 Stopping existing containers..."
docker-compose -f docker-compose.dev.yml down

# Start the development environment
echo "🔧 Starting development containers..."
docker-compose -f docker-compose.dev.yml up -d

# Wait for services to be ready
echo "⏳ Waiting for services to start..."
sleep 10

# Check if services are running
echo "🔍 Checking service status..."
docker-compose -f docker-compose.dev.yml ps

echo ""
echo "✅ Development environment is ready!"
echo "🌐 Application: http://localhost:8080"
echo "🗄️  Database: localhost:3307 (external), db:3306 (internal)"
echo "📊 phpMyAdmin: http://localhost:8081"
echo ""
echo "💡 Code changes will be reflected immediately without restarting containers!"
echo "🛑 To stop: docker-compose -f docker-compose.dev.yml down"
