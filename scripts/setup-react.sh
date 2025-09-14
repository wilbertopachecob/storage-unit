#!/bin/bash

# Setup script for React frontend
# This script sets up the React application for the Storage Unit Management System

echo "🚀 Setting up React frontend for Storage Unit Management System..."

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 16+ first."
    echo "   Visit: https://nodejs.org/"
    exit 1
fi

# Check Node.js version
NODE_VERSION=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 16 ]; then
    echo "❌ Node.js version 16+ is required. Current version: $(node -v)"
    exit 1
fi

echo "✅ Node.js version: $(node -v)"

# Navigate to React frontend directory
cd react-frontend

# Check if package.json exists
if [ ! -f "package.json" ]; then
    echo "❌ package.json not found. Please run this script from the project root."
    exit 1
fi

echo "📦 Installing dependencies..."
npm install

if [ $? -ne 0 ]; then
    echo "❌ Failed to install dependencies"
    exit 1
fi

echo "✅ Dependencies installed successfully"

# Create .env file if it doesn't exist
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file..."
    cat > .env << EOF
# React App Configuration
REACT_APP_API_URL=http://localhost:8080/api

# Development settings
GENERATE_SOURCEMAP=false
EOF
    echo "✅ .env file created"
fi

echo ""
echo "🎉 React frontend setup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Start the PHP backend: docker-compose up -d"
echo "2. Start the React development server: npm start"
echo "3. Open http://localhost:3000 in your browser"
echo ""
echo "🔧 Available scripts:"
echo "  npm start          - Start development server"
echo "  npm run build      - Build for production"
echo "  npm test           - Run tests"
echo "  npm run dev        - Start with API URL configured"
echo ""
