#!/bin/bash
# Docker Migration Script
# Runs database migrations inside Docker container

echo "Starting Docker migration..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Error: Docker is not running. Please start Docker first."
    exit 1
fi

# Check if containers are running
if ! docker-compose ps | grep -q "Up"; then
    echo "Starting Docker containers..."
    docker-compose up -d
    echo "Waiting for database to be ready..."
    sleep 10
fi

# Run migration inside the web container
echo "Running migration inside Docker container..."
docker-compose exec web php scripts/run-migrations.php

echo "Migration completed!"
