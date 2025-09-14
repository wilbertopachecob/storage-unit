#!/bin/bash

# Test script for React-PHP integration
# This script tests the complete integration between React frontend and PHP backend

set -e

echo "🧪 Testing React-PHP Integration..."

# Check if we're in the right directory
if [ ! -f "composer.json" ] || [ ! -d "react-frontend" ]; then
    echo "❌ Please run this script from the project root directory"
    exit 1
fi

# Check if React app is built
if [ ! -f "public/react-files.json" ]; then
    echo "❌ React app not built. Please run ./scripts/build-react.sh first"
    exit 1
fi

echo "✅ React app build files found"

# Check if required files exist
REQUIRED_FILES=(
    "public/analytics.php"
    "public/api/analytics.php"
    "public/react-files.json"
    "public/static/css"
    "public/static/js"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -e "$file" ]; then
        echo "❌ Required file/directory missing: $file"
        exit 1
    fi
done

echo "✅ All required files present"

# Check if PHP dependencies are installed
if [ ! -d "vendor" ]; then
    echo "❌ PHP dependencies not installed. Please run composer install"
    exit 1
fi

echo "✅ PHP dependencies installed"

# Check if React dependencies are installed
if [ ! -d "react-frontend/node_modules" ]; then
    echo "❌ React dependencies not installed. Please run ./scripts/setup-react.sh"
    exit 1
fi

echo "✅ React dependencies installed"

# Test PHP syntax
echo "🔍 Testing PHP syntax..."
php -l public/analytics.php
php -l public/api/analytics.php

if [ $? -eq 0 ]; then
    echo "✅ PHP syntax is valid"
else
    echo "❌ PHP syntax errors found"
    exit 1
fi

# Test React build
echo "🔍 Testing React build..."
cd react-frontend
npm run type-check

if [ $? -eq 0 ]; then
    echo "✅ React TypeScript compilation successful"
else
    echo "❌ React TypeScript compilation failed"
    exit 1
fi

cd ..

# Test if manifest file is valid JSON
echo "🔍 Testing manifest file..."
if python3 -m json.tool public/react-files.json > /dev/null 2>&1; then
    echo "✅ Manifest file is valid JSON"
else
    echo "❌ Manifest file is not valid JSON"
    exit 1
fi

# Check if static files exist
CSS_FILE=$(python3 -c "import json; data=json.load(open('public/react-files.json')); print(data['css'])")
JS_FILE=$(python3 -c "import json; data=json.load(open('public/react-files.json')); print(data['js'])")

if [ -f "public/static/css/$CSS_FILE" ]; then
    echo "✅ CSS file found: $CSS_FILE"
else
    echo "❌ CSS file not found: $CSS_FILE"
    exit 1
fi

if [ -f "public/static/js/$JS_FILE" ]; then
    echo "✅ JS file found: $JS_FILE"
else
    echo "❌ JS file not found: $JS_FILE"
    exit 1
fi

echo ""
echo "🎉 All tests passed! Integration is working correctly."
echo ""
echo "📋 Next steps:"
echo "1. Start the PHP backend: docker-compose up -d"
echo "2. Visit http://localhost:8080/analytics.php"
echo "3. Verify the React dashboard loads correctly"
echo ""
echo "🔧 For development:"
echo "1. Start React dev server: cd react-frontend && npm start"
echo "2. Start PHP backend: docker-compose up -d"
echo "3. Visit http://localhost:3000 for React dev server"
