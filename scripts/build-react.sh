#!/bin/bash

# Build React app and copy to public directory
# This script builds the React frontend and copies the built files to the public directory

set -e

echo "🚀 Building React app for Storage Unit Management System..."

# Navigate to react-frontend directory
cd "$(dirname "$0")/../react-frontend"

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Build the React app with proper API URL
echo "🔨 Building React app..."
REACT_APP_API_URL=/api npm run build

if [ $? -ne 0 ]; then
    echo "❌ React build failed"
    exit 1
fi

# Create static directories if they don't exist
echo "📁 Creating static directories..."
cd ..
mkdir -p public/static/css
mkdir -p public/static/js
mkdir -p public/static/media

# Copy built files to public directory
echo "📋 Copying built files to public directory..."

# Copy CSS files
if [ -d "react-frontend/build/static/css" ]; then
    cp -r react-frontend/build/static/css/* public/static/css/
fi

# Copy JS files
if [ -d "react-frontend/build/static/js" ]; then
    cp -r react-frontend/build/static/js/* public/static/js/
fi

# Copy media files
if [ -d "react-frontend/build/static/media" ]; then
    cp -r react-frontend/build/static/media/* public/static/media/
fi

# Copy main HTML file
if [ -f "react-frontend/build/index.html" ]; then
    cp react-frontend/build/index.html public/react-app.html
fi

# Copy manifest and other files
if [ -f "react-frontend/build/manifest.json" ]; then
    cp react-frontend/build/manifest.json public/react-manifest.json
fi

# Create a simple manifest with the actual file names
echo "📝 Creating file manifest..."

# Find the actual CSS file (get the most recent one)
CSS_FILE=$(find react-frontend/build/static/css -name "main.*.css" -type f -exec ls -t {} + | head -1 | xargs basename)
JS_FILE=$(find react-frontend/build/static/js -name "main.*.js" -type f -exec ls -t {} + | head -1 | xargs basename)

# Update the analytics.php file with the correct file names
echo "📝 Updating analytics.php with correct file names..."
sed -i.bak "s/css: 'main\.[^']*'/css: '$CSS_FILE'/g" public/analytics.php
sed -i.bak "s/js: 'main\.[^']*'/js: '$JS_FILE'/g" public/analytics.php
rm -f public/analytics.php.bak

echo "📋 File manifest created:"
echo "  CSS: $CSS_FILE"
echo "  JS: $JS_FILE"

echo ""
echo "✅ React app built and deployed successfully!"
echo ""
echo "📋 Files copied to public directory:"
echo "  - CSS: public/static/css/"
echo "  - JS: public/static/js/"
echo "  - Media: public/static/media/"
echo "  - HTML: public/react-app.html"
echo ""
echo "🌐 You can now access the analytics dashboard at /analytics.php"
echo ""