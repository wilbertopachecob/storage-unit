#!/bin/bash

# Build React app and copy to public directory
# This script builds the React frontend and copies the built files to the public directory

set -e

echo "Building React app..."

# Navigate to react-frontend directory
cd "$(dirname "$0")/../react-frontend"

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "Installing dependencies..."
    npm install
fi

# Build the React app
echo "Building React app..."
npm run build

# Copy built files to public directory
echo "Copying built files to public directory..."
cd ..
cp -r react-frontend/build/* public/

echo "React app built and deployed successfully!"
echo "You can now access the analytics dashboard at /analytics.php"