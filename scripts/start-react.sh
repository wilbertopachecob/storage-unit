#!/bin/bash

# Start React development server
# This script starts the React development server with proper configuration

echo "🚀 Starting React development server..."

# Check if we're in the right directory
if [ ! -f "react-frontend/package.json" ]; then
    echo "❌ Please run this script from the project root directory"
    exit 1
fi

# Navigate to React frontend directory
cd react-frontend

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies first..."
    npm install
fi

# Set environment variables
export REACT_APP_API_URL=http://localhost:8080/api

echo "🌐 Starting React app on http://localhost:3000"
echo "🔗 API URL: $REACT_APP_API_URL"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start the development server
npm start
