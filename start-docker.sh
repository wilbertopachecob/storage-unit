#!/bin/bash

echo "🐳 Starting Storage Unit Management System with Docker..."

# Check if Docker is running
echo "Checking Docker status..."
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop first."
    echo "   You can start it from Applications or run: open -a Docker"
    echo ""
    echo "Waiting for Docker to start..."
    
    # Wait for Docker to start (max 60 seconds)
    for i in {1..12}; do
        if docker info > /dev/null 2>&1; then
            echo "✅ Docker is now running!"
            break
        fi
        echo "   Waiting... ($i/12)"
        sleep 5
    done
    
    if ! docker info > /dev/null 2>&1; then
        echo "❌ Docker failed to start. Please check Docker Desktop."
        exit 1
    fi
fi

echo "✅ Docker is running!"

# Start the containers
echo "🚀 Starting containers..."
docker-compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to start..."
sleep 10

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
