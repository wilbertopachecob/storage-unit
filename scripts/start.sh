#!/bin/bash

echo "🚀 Starting Storage Unit Management System..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop first."
    exit 1
fi

echo "✅ Docker is running"

# Start the containers
echo "🐳 Starting containers..."
docker-compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to start..."
sleep 5

# Check container status
echo "📊 Container status:"
docker-compose ps

echo ""
echo "🎉 Setup complete!"
echo ""
echo "📋 Access your application:"
echo "   🌐 Web App: http://localhost:8080"
echo "   🗄️  phpMyAdmin: http://localhost:8081"
echo ""
echo "🔑 Test accounts:"
echo "   📧 Email: admin@example.com"
echo "   🔒 Password: password123"
echo ""
echo "🔧 Useful commands:"
echo "   📊 View logs: docker-compose logs -f"
echo "   🛑 Stop containers: docker-compose down"
echo "   🔄 Restart: docker-compose restart"
echo ""
